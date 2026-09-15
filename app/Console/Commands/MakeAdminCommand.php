<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Creates an admin account interactively. The only way to create the first one — there is
 * no seeder and no env-var credential, so nothing in this repo ever knows a real password.
 * The password is typed once and hashed straight into the database; it never sits in
 * .env, a deploy log, or a shell's history file.
 */
class MakeAdminCommand extends Command
{
    protected $signature = 'app:make-admin {--name=} {--email=} {--password=}';

    protected $description = 'Create an admin account, prompting for its details rather than reading them from the environment';

    public function handle(): int
    {
        $name = $this->option('name') ?? text(label: 'Name', required: true);

        // Checked again below regardless of source: the interactive validate() callback
        // only runs for a typed answer, never for a value handed in via --email.
        $email = $this->option('email') ?? text(
            label: 'Email address',
            required: true,
            validate: fn (string $value) => $this->emailError($value),
        );

        $plainPassword = $this->option('password') ?? password(
            label: 'Password',
            required: true,
            validate: fn (string $value) => $this->passwordError($value),
        );

        if ($error = $this->emailError($email) ?? $this->passwordError($plainPassword)) {
            $this->components->error($error);

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            // The model casts this to a hash on save; nothing here writes it out plain.
            'password' => $plainPassword,
            'role' => UserRole::Admin,
            'locale' => config('app.locale'),
            'is_active' => true,
        ]);

        $this->components->info("Admin account created: {$user->email}");

        return self::SUCCESS;
    }

    private function emailError(string $value): ?string
    {
        return match (true) {
            ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'Enter a valid email address.',
            User::where('email', $value)->exists() => 'An account with this email already exists.',
            default => null,
        };
    }

    private function passwordError(string $value): ?string
    {
        return Validator::make(
            ['password' => $value],
            ['password' => Password::defaults()],
        )->errors()->first('password') ?: null;
    }
}
