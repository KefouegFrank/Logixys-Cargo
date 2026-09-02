<?php

namespace Tests\Feature\Http;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_french(): void
    {
        $this->get('/')->assertRedirect('/fr');
    }

    public function test_unsupported_locale_is_rejected(): void
    {
        $this->get('/pt/suivi')->assertNotFound();
    }

    public function test_every_supported_locale_is_served(): void
    {
        foreach (array_keys(config('locales')) as $locale) {
            $this->get("/{$locale}/suivi")->assertOk();
        }
    }

    public function test_lookup_form_renders(): void
    {
        $this->get('/fr/suivi')->assertOk()->assertSee(__('tracking.form_heading'));
    }

    public function test_valid_tracking_number_shows_whitelisted_data_only(): void
    {
        $shipment = $this->shipment([
            'shipper_name' => 'Jean Martin',
            'shipper_email' => 'jean@example.com',
            'shipper_phone' => '0600000000',
            'shipper_address' => '12 Rue Secrete',
            'declared_value' => 50000,
        ]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertSee($shipment->tracking_number);
        $response->assertSee('J. M.');
        $response->assertDontSee('jean@example.com');
        $response->assertDontSee('0600000000');
        $response->assertDontSee('12 Rue Secrete');
        $response->assertDontSee('50000');
        $response->assertDontSee('Jean Martin');
    }

    public function test_normalizes_input_before_lookup(): void
    {
        $shipment = $this->shipment();
        $messy = strtolower(substr($shipment->tracking_number, 0, 4)).'-'.strtolower(substr($shipment->tracking_number, 4));

        $this->get("/fr/suivi/{$messy}")->assertOk()->assertSee($shipment->tracking_number);
    }

    public function test_malformed_number_shows_generic_not_found(): void
    {
        $this->get('/fr/suivi/not-a-real-number')
            ->assertOk()
            ->assertSee(__('tracking.not_found_heading'));
    }

    public function test_nonexistent_number_shows_the_same_generic_not_found(): void
    {
        $this->get('/fr/suivi/LGXY000000000')
            ->assertOk()
            ->assertSee(__('tracking.not_found_heading'));
    }

    public function test_form_redirects_to_the_result_page(): void
    {
        $shipment = $this->shipment();

        $this->get("/fr/suivi?number={$shipment->tracking_number}")
            ->assertRedirect("/fr/suivi/{$shipment->tracking_number}");
    }

    public function test_result_pages_are_not_indexed(): void
    {
        $shipment = $this->shipment();

        $this->get("/fr/suivi/{$shipment->tracking_number}")
            ->assertSee('<meta name="robots" content="noindex">', false);
    }

    public function test_shows_the_four_step_bar_for_an_in_progress_status(): void
    {
        $shipment = $this->shipment(['status' => ShipmentStatus::InTransit]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertSee(__('shipment.status.IN_TRANSIT'));
        $response->assertSee('<ol', false);
        $response->assertDontSee('border-amber-300', false);
        $response->assertDontSee('border-red-300', false);
    }

    public function test_shows_the_exception_banner_instead_of_the_bar_for_on_hold(): void
    {
        $shipment = $this->shipment(['status' => ShipmentStatus::OnHold]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertSee(__('shipment.status.ON_HOLD'));
        $response->assertSee('border-amber-300', false);
        $response->assertDontSee('<ol', false); // the four-step bar is not rendered
    }

    public function test_shows_the_danger_exception_banner_for_cancelled(): void
    {
        $shipment = $this->shipment(['status' => ShipmentStatus::Cancelled]);

        $this->get("/fr/suivi/{$shipment->tracking_number}")
            ->assertOk()
            ->assertSee('border-red-300', false);
    }

    public function test_timeline_shows_public_events_most_recent_first(): void
    {
        $shipment = $this->shipment();
        $agent = User::first();

        $shipment->events()->create([
            'status' => ShipmentStatus::Pending, 'location_label' => 'Paris',
            'occurred_at' => now()->subDays(2), 'is_public' => true, 'created_by' => $agent->id,
        ]);
        $shipment->events()->create([
            'status' => ShipmentStatus::PickedUp, 'location_label' => 'Lyon',
            'occurred_at' => now()->subDay(), 'is_public' => true, 'created_by' => $agent->id,
        ]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertSeeInOrder([__('shipment.status.PICKED_UP'), 'Lyon', __('shipment.status.PENDING'), 'Paris']);
    }

    public function test_timeline_excludes_internal_events_and_remarks(): void
    {
        $shipment = $this->shipment();
        $agent = User::first();

        $shipment->events()->create([
            'status' => ShipmentStatus::InTransit, 'location_label' => 'Secret Depot',
            'remarks' => 'Confidential internal note', 'occurred_at' => now(),
            'is_public' => false, 'created_by' => $agent->id,
        ]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertDontSee('Secret Depot');
        $response->assertDontSee('Confidential internal note');
        $response->assertDontSee(__('tracking.timeline_heading'));
    }

    public function test_lookup_is_rate_limited(): void
    {
        $shipment = $this->shipment();

        for ($i = 0; $i < 10; $i++) {
            $this->get("/fr/suivi/{$shipment->tracking_number}")->assertOk();
        }

        $this->get("/fr/suivi/{$shipment->tracking_number}")->assertStatus(429);
    }

    private function shipment(array $overrides = []): Shipment
    {
        $user = User::firstOrCreate(
            ['email' => 'seed@example.com'],
            ['name' => 'Seed User', 'password' => 'password', 'role' => 'admin'],
        );

        return Shipment::create(array_merge([
            'tracking_number' => 'LGXY'.fake()->unique()->bothify('#########'),
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris', 'shipper_country' => 'FR',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $user->id,
        ], $overrides))->fresh();
    }
}
