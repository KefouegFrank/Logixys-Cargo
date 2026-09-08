<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_away_from_the_panel(): void
    {
        $this->get('/admin/shipments')->assertRedirect();
        $this->get('/admin/shipments/create')->assertRedirect();
    }

    public function test_an_active_admin_reaches_every_shipment_page(): void
    {
        $this->actingAs($this->user(UserRole::Admin, true));

        $this->get('/admin/shipments')->assertOk();
        $this->get('/admin/shipments/create')->assertOk();
    }

    public function test_an_active_agent_reaches_the_panel(): void
    {
        $this->actingAs($this->user(UserRole::Agent, true));

        $this->get('/admin/shipments')->assertOk();
        $this->get('/admin/shipments/create')->assertOk();
    }

    public function test_a_deactivated_user_is_locked_out_whatever_their_role(): void
    {
        $this->actingAs($this->user(UserRole::Admin, false));
        $this->get('/admin/shipments')->assertForbidden();
    }

    private function user(UserRole $role, bool $active): User
    {
        return User::create([
            'name' => 'Audit '.$role->value.($active ? '' : ' inactive'),
            'email' => $role->value.($active ? '' : '-inactive').'@audit.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => $active,
        ]);
    }
}
