<?php

namespace App\Http\Controllers;

use App\Models\MailSuppression;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resend delivery events. Only bounces and complaints are acted on; the rest are
 * acknowledged so Resend stops retrying them.
 */
class ResendWebhookController extends Controller
{
    /** Replays older than this are rejected even with a valid signature. */
    private const TOLERANCE_SECONDS = 300;

    public function __invoke(Request $request): JsonResponse
    {
        $secret = config('services.resend.webhook_secret');

        if (blank($secret)) {
            Log::warning('Resend webhook received but no signing secret is configured.');

            return response()->json(['message' => 'Webhook not configured.'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if (! $this->signatureIsValid($request, $secret)) {
            return response()->json(['message' => 'Invalid signature.'], Response::HTTP_UNAUTHORIZED);
        }

        $type = (string) $request->input('type');
        $email = $request->input('data.to.0') ?? $request->input('data.email');

        $reason = match ($type) {
            'email.bounced' => MailSuppression::REASON_BOUNCED,
            'email.complained' => MailSuppression::REASON_COMPLAINED,
            default => null,
        };

        if ($reason !== null && filled($email)) {
            MailSuppression::record($email, $reason, $request->input('data.bounce.message'));
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * Resend signs through Svix: HMAC-SHA256 over "{id}.{timestamp}.{body}", keyed with the
     * base64 secret after its whsec_ prefix.
     */
    private function signatureIsValid(Request $request, string $secret): bool
    {
        $id = (string) $request->header('svix-id');
        $timestamp = (string) $request->header('svix-timestamp');
        $signatures = (string) $request->header('svix-signature');

        if (blank($id) || blank($timestamp) || blank($signatures)) {
            return false;
        }

        if (! ctype_digit($timestamp) || abs(time() - (int) $timestamp) > self::TOLERANCE_SECONDS) {
            return false;
        }

        $key = base64_decode(str_starts_with($secret, 'whsec_') ? substr($secret, 6) : $secret, true);

        if ($key === false) {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', "{$id}.{$timestamp}.{$request->getContent()}", $key, true));

        foreach (explode(' ', $signatures) as $candidate) {
            // Each entry is "v1,<base64>"; a rotated secret means several are sent at once.
            [$version, $signature] = array_pad(explode(',', $candidate, 2), 2, '');

            if ($version === 'v1' && $signature !== '' && hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }
}
