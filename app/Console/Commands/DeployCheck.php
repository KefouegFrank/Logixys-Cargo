<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Asserts the settings that cannot be caught by the test suite, because phpunit.xml
 * overrides them. Run it on the server after every deploy; a non-zero exit means the
 * configuration is not safe to serve the public with.
 */
class DeployCheck extends Command
{
    protected $signature = 'deploy:check';

    protected $description = 'Verify the environment is configured safely for production';

    /** Passwords that must never authenticate, whatever the policy allows. */
    private const KNOWN_PASSWORDS = ['password', 'secret', 'admin', '123456'];

    public function handle(): int
    {
        $failures = 0;
        $warnings = 0;

        foreach ($this->checks() as [$label, $passed, $remedy]) {
            if ($passed) {
                $this->line("  <fg=green>OK</>    {$label}");

                continue;
            }

            $failures++;
            $this->line("  <fg=red>FAIL</>  {$label}");
            $this->line("        {$remedy}");
        }

        foreach ($this->advisories() as [$label, $passed, $remedy]) {
            if ($passed) {
                $this->line("  <fg=green>OK</>    {$label}");

                continue;
            }

            $warnings++;
            $this->line("  <fg=yellow>WARN</>  {$label}");
            $this->line("        {$remedy}");
        }

        $this->newLine();

        if ($failures > 0) {
            $this->error("{$failures} check(s) failed. Do not serve this configuration.");

            return self::FAILURE;
        }

        $this->info($warnings > 0 ? "Ready, with {$warnings} advisory item(s)." : 'Ready.');

        return self::SUCCESS;
    }

    /** @return list<array{string, bool, string}> */
    private function checks(): array
    {
        return [
            [
                'APP_ENV is production',
                app()->environment('production'),
                'Set APP_ENV=production; a non-production environment loosens several defaults.',
            ],
            [
                'APP_DEBUG is off',
                ! config('app.debug'),
                'Set APP_DEBUG=false. With it on, any error page prints the database and API credentials.',
            ],
            [
                'APP_KEY is set',
                filled(config('app.key')),
                'Run php artisan key:generate; without it sessions and encrypted values are unprotected.',
            ],
            [
                'Session cookie is marked Secure',
                config('session.secure') === true,
                'Set SESSION_SECURE_COOKIE=true so the panel session cookie is never sent in clear.',
            ],
            [
                'No account uses a well-known password',
                $this->noKnownPasswordsInUse(),
                'Change it from the panel. A seeded or placeholder password is a working login.',
            ],
        ];
    }

    /** @return list<array{string, bool, string}> */
    private function advisories(): array
    {
        return [
            [
                'Logs are not at debug level',
                ! in_array(config('logging.channels.single.level', 'debug'), ['debug'], true),
                'Set LOG_LEVEL=error; at debug the logs collect customer names and addresses.',
            ],
            [
                'Session payloads are encrypted at rest',
                (bool) config('session.encrypt'),
                'Consider SESSION_ENCRYPT=true; sessions share the application database.',
            ],
        ];
    }

    private function noKnownPasswordsInUse(): bool
    {
        foreach (User::query()->select(['id', 'password'])->cursor() as $user) {
            foreach (self::KNOWN_PASSWORDS as $known) {
                if (Hash::check($known, $user->password)) {
                    return false;
                }
            }
        }

        return true;
    }
}
