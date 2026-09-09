<?php

namespace Tests\Feature\Http;

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Models\PaymentMode;
use App\Models\Shipment;
use App\Models\ShipmentMode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrackingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // ShipmentEvent geocodes a location_label on write; no test here should reach
        // the real Nominatim service.
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '48.8566', 'lon' => '2.3522']])]);
    }

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

    public function test_a_valid_tracking_number_shows_the_whole_record(): void
    {
        $shipment = $this->shipment([
            'shipper_name' => 'Jean Martin',
            'shipper_company' => 'Pieces Auto Distribution',
            'shipper_email' => 'jean@example.com',
            'shipper_phone' => '0600000000',
            'shipper_address' => '12 Rue des Docteurs Charcot',
            'receiver_name' => 'Miguel Sanchez',
            'receiver_email' => 'miguel@example.com',
            'receiver_phone' => '+34639204954',
            'carrier_name' => 'COLIS EXPRESS EU',
            'carrier_reference' => 'CEE36',
            'goods_description' => 'Jantes mercedes ML de 20 pouces',
            'declared_value' => 50000,
            'freight_cost' => 750,
            'payment_mode' => PaymentMode::VIREMENT,
            'payment_status' => 'unpaid',
            'pickup_time' => '09:00',
            'departure_time' => '15:00',
        ]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertSee($shipment->tracking_number);

        // The number is the credential; whoever holds it gets the same record the agent sees.
        foreach ([
            'Jean Martin', 'Pieces Auto Distribution', 'jean@example.com', '0600000000',
            '12 Rue des Docteurs Charcot', 'Miguel Sanchez', 'miguel@example.com', '+34639204954',
            'COLIS EXPRESS EU', 'CEE36', 'Jantes mercedes ML de 20 pouces',
            '50 000,00', '750,00', '09:00', '15:00',
        ] as $expected) {
            $response->assertSee($expected, false);
        }

        $response->assertSee(PaymentMode::VIREMENT);
        $response->assertSee(__('shipment.payment_status.unpaid'));
    }

    public function test_the_charges_breakdown_is_shown_in_full(): void
    {
        $shipment = $this->shipment([
            'freight_cost' => 890, 'insurance_cost' => 40, 'customs_cost' => 60,
            'other_cost' => 0, 'tax_rate' => 20, 'tax_label' => 'TVA', 'currency' => 'EUR',
        ]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertSee(__('tracking.charges_heading'));
        $response->assertSee('890,00 EUR', false);   // fret
        $response->assertSee('990,00 EUR', false);   // total HT
        $response->assertSee('198,00 EUR', false);   // TVA at 20%
        $response->assertSee('1 188,00 EUR', false); // total TTC
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
        $this->get('/fr/suivi/LGXY000000000-CARGO')
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

    public function test_shows_the_origin_and_destination_the_agent_entered(): void
    {
        $shipment = $this->shipment([
            // Crossed against the parties on purpose: the page used to derive the leg from
            // the shipper's and receiver's own cities, so it showed these two the wrong
            // way round whenever they disagreed with the picked locations.
            'shipper_city' => 'Lyon', 'shipper_country' => 'FR',
            'receiver_city' => 'Douala', 'receiver_country' => 'CM',
            'origin_label' => 'Douala, CM',
            'destination_label' => 'Roissy CDG, FR',
        ]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertSeeInOrder([__('tracking.result_origin'), 'Douala, CM']);
        $response->assertSeeInOrder([__('tracking.result_destination'), 'Roissy CDG, FR']);
    }

    public function test_the_map_shows_the_pin_the_agent_set_on_the_latest_event(): void
    {
        $shipment = $this->shipment();
        $agent = User::first();

        $shipment->events()->create([
            'status' => ShipmentStatus::PickedUp, 'location_label' => 'Marseille',
            'location_lat' => 43.2965, 'location_lng' => 5.3698, 'is_manual_position' => true,
            'occurred_at' => now()->subDays(2), 'is_public' => true, 'created_by' => $agent->id,
        ]);
        $shipment->events()->create([
            'status' => ShipmentStatus::InTransit, 'location_label' => 'Paris CDG',
            'location_lat' => 49.0097, 'location_lng' => 2.5479, 'is_manual_position' => true,
            'occurred_at' => now()->subDay(), 'is_public' => true, 'created_by' => $agent->id,
        ]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertSee(__('tracking.map_heading'));
        $response->assertSee('vendor/leaflet/leaflet.js', false);
        $response->assertSee(__('tracking.map_here'));

        // Only the most recent pin: no earlier stop, and no origin/destination coordinates.
        $response->assertSee('49.0097', false);
        $response->assertDontSee('43.2965', false);
        $response->assertDontSee((string) $shipment->destination_lat, false);
    }

    public function test_the_map_is_left_out_when_nothing_has_coordinates(): void
    {
        $shipment = $this->shipment([
            'origin_label' => '', 'origin_lat' => null, 'origin_lng' => null,
            'destination_label' => '', 'destination_lat' => null, 'destination_lng' => null,
        ]);

        $response = $this->get("/fr/suivi/{$shipment->tracking_number}");

        $response->assertOk();
        $response->assertDontSee(__('tracking.map_heading'));
        $response->assertDontSee('id="tracking-map"', false);
        $response->assertDontSee('vendor/leaflet/leaflet.js', false);
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
            'tracking_number' => 'LGXY'.fake()->unique()->bothify('#########').'-CARGO',
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DOOR_TO_DOOR,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris', 'shipper_country' => 'FR',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $user->id,
        ], $overrides))->fresh();
    }
}
