<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageAcknowledged;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact');
    }

    public function store(ContactRequest $request, string $locale): RedirectResponse
    {
        $message = ContactMessage::create([
            ...$request->safe()->except([ContactRequest::HONEYPOT]),
            'locale' => $locale,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        // Queued, so a slow or unreachable Resend never holds up the response. The row
        // is already saved either way, so nothing is lost if delivery later fails.
        try {
            Mail::to(config('brand.contact.email'))->queue(new ContactMessageReceived($message));
            Mail::to($message->email, $message->name)->queue(new ContactMessageAcknowledged($message));
        } catch (\Throwable $exception) {
            Log::error('Contact mail could not be queued.', [
                'contact_message_id' => $message->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('contact', ['locale' => $locale])
            ->with('contact.sent', true)
            ->withFragment('contact-form');
    }
}
