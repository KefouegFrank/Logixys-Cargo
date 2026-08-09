<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@logixyscargo.fr',
            'password' => 'password',
            'role' => UserRole::Admin,
            'locale' => 'fr',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Agent User',
            'email' => 'agent@logixyscargo.fr',
            'password' => 'password',
            'role' => UserRole::Agent,
            'locale' => 'fr',
            'is_active' => true,
        ]);
    }
}
