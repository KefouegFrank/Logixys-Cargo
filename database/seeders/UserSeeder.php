<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * Creates the one account needed to sign in for the first time. Everyone else is added
     * from the panel, so no other credential is ever written by a seeder.
     */
    public function run(): void
    {
        $email = config('seeding.admin_email');
        $password = config('seeding.admin_password');

        if (blank($email) || blank($password)) {
            throw new RuntimeException(
                'Set SEED_ADMIN_EMAIL and SEED_ADMIN_PASSWORD before seeding the first administrator.'
            );
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('seeding.admin_name'),
                'password' => $password,
                'role' => UserRole::Admin,
                'locale' => config('app.locale'),
                'is_active' => true,
            ],
        );
    }
}
