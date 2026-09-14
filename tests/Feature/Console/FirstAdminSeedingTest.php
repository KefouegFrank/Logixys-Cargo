<?php

namespace Tests\Feature\Console;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class FirstAdminSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_refuses_to_run_without_credentials_in_the_environment(): void
    {
        config(['seeding.admin_email' => null, 'seeding.admin_password' => null]);

        $this->expectException(RuntimeException::class);

        $this->seed(UserSeeder::class);
    }

    public function test_seeding_refuses_to_run_with_only_half_the_credentials(): void
    {
        config(['seeding.admin_email' => 'ops@example.test', 'seeding.admin_password' => null]);

        $this->expectException(RuntimeException::class);

        $this->seed(UserSeeder::class);
    }

    public function test_the_seeded_admin_comes_from_the_environment(): void
    {
        config([
            'seeding.admin_email' => 'ops@example.test',
            'seeding.admin_password' => 'a-password-only-this-server-knows',
            'seeding.admin_name' => 'Ops',
        ]);

        $this->seed(UserSeeder::class);

        $admin = User::where('email', 'ops@example.test')->sole();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(Hash::check('a-password-only-this-server-knows', $admin->password));
    }

    // The point of the change: no seeder may leave behind a password known off the server.
    public function test_seeding_leaves_no_account_using_a_known_password(): void
    {
        config([
            'seeding.admin_email' => 'ops@example.test',
            'seeding.admin_password' => 'a-password-only-this-server-knows',
        ]);

        $this->seed(UserSeeder::class);

        foreach (User::all() as $user) {
            $this->assertFalse(Hash::check('password', $user->password));
        }
    }

    public function test_seeding_twice_does_not_duplicate_the_admin(): void
    {
        config([
            'seeding.admin_email' => 'ops@example.test',
            'seeding.admin_password' => 'a-password-only-this-server-knows',
        ]);

        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(1, User::where('email', 'ops@example.test')->count());
    }
}
