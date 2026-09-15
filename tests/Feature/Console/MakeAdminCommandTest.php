<?php

namespace Tests\Feature\Console;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MakeAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_an_active_admin_from_the_given_options(): void
    {
        $this->artisan('app:make-admin', [
            '--name' => 'Ops',
            '--email' => 'ops@example.test',
            '--password' => 'a-password-only-this-operator-knows',
        ])->assertSuccessful();

        $admin = User::where('email', 'ops@example.test')->sole();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(Hash::check('a-password-only-this-operator-knows', $admin->password));
    }

    public function test_a_password_under_the_floor_is_refused(): void
    {
        $this->artisan('app:make-admin', [
            '--name' => 'Ops',
            '--email' => 'ops@example.test',
            '--password' => 'short',
        ])->assertFailed();

        $this->assertFalse(User::where('email', 'ops@example.test')->exists());
    }

    public function test_an_email_already_in_use_is_refused_at_the_prompt(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);

        // Options skip the interactive validate() callback, so this exercises the model's
        // own constraint instead — the account still must not be created twice.
        $this->artisan('app:make-admin', [
            '--name' => 'Ops',
            '--email' => 'taken@example.test',
            '--password' => 'a-password-only-this-operator-knows',
        ])->assertFailed();
    }
}
