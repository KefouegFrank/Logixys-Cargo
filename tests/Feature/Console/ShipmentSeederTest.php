<?php

namespace Tests\Feature\Console;

use App\Enums\UserRole;
use App\Models\Shipment;
use App\Models\User;
use Database\Seeders\ShipmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ShipmentSeederTest extends TestCase
{
    use RefreshDatabase;

    // Nothing creates a first user any more (see F-6/app:make-admin), so this seeder has
    // to fail loudly rather than crash on a null id or silently attribute rows to no one.
    public function test_refuses_to_run_with_no_users_at_all(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('app:make-admin');

        $this->seed(ShipmentSeeder::class);
    }

    public function test_runs_once_an_admin_exists(): void
    {
        User::create([
            'name' => 'First Admin', 'email' => 'admin@example.test', 'password' => 'password',
            'role' => UserRole::Admin, 'is_active' => true,
        ]);

        $this->seed(ShipmentSeeder::class);

        $this->assertTrue(Shipment::query()->exists());
    }

    // The full chain, not just this seeder in isolation — confirms DatabaseSeeder still
    // works now that it no longer has a UserSeeder step to run before this one.
    public function test_the_full_database_seeder_chain_runs_once_an_admin_exists(): void
    {
        User::create([
            'name' => 'First Admin', 'email' => 'admin@example.test', 'password' => 'password',
            'role' => UserRole::Admin, 'is_active' => true,
        ]);

        $this->seed();

        $this->assertTrue(Shipment::query()->exists());
    }
}
