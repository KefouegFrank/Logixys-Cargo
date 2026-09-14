<?php

namespace Tests\Feature\Console;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeployCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_production_ready_environment_passes(): void
    {
        $this->productionConfig();

        $this->artisan('deploy:check')->assertSuccessful();
    }

    public function test_debug_mode_fails_the_check(): void
    {
        $this->productionConfig();
        config(['app.debug' => true]);

        $this->artisan('deploy:check')
            ->expectsOutputToContain('APP_DEBUG is off')
            ->assertFailed();
    }

    public function test_a_non_production_environment_fails_the_check(): void
    {
        $this->productionConfig();
        app()->detectEnvironment(fn () => 'local');

        $this->artisan('deploy:check')->assertFailed();
    }

    public function test_an_insecure_session_cookie_fails_the_check(): void
    {
        $this->productionConfig();
        config(['session.secure' => null]);

        $this->artisan('deploy:check')
            ->expectsOutputToContain('Session cookie is marked Secure')
            ->assertFailed();
    }

    public function test_a_missing_app_key_fails_the_check(): void
    {
        $this->productionConfig();
        config(['app.key' => '']);

        $this->artisan('deploy:check')->assertFailed();
    }

    // Catches a seeded or placeholder credential that survived into production.
    public function test_an_account_with_a_known_password_fails_the_check(): void
    {
        $this->productionConfig();

        User::create([
            'name' => 'Leftover', 'email' => 'leftover@example.test', 'password' => 'password',
            'role' => UserRole::Agent, 'is_active' => true,
        ]);

        $this->artisan('deploy:check')
            ->expectsOutputToContain('No account uses a well-known password')
            ->assertFailed();
    }

    public function test_debug_logging_is_flagged_without_failing_the_deploy(): void
    {
        $this->productionConfig();
        config(['logging.channels.single.level' => 'debug']);

        $this->artisan('deploy:check')
            ->expectsOutputToContain('Logs are not at debug level')
            ->assertSuccessful();
    }

    private function productionConfig(): void
    {
        app()->detectEnvironment(fn () => 'production');

        config([
            'app.debug' => false,
            'app.key' => 'base64:'.base64_encode(random_bytes(32)),
            'session.secure' => true,
            'logging.channels.single.level' => 'error',
        ]);
    }
}
