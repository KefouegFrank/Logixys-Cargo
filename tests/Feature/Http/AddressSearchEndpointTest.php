<?php

namespace Tests\Feature\Http;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AddressSearchEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'photon.komoot.io/*' => Http::response(['features' => []]),
            'api-adresse.data.gouv.fr/*' => Http::response(['features' => [[
                'properties' => ['label' => '10 Rue Rochambeau 57000 Metz', 'score' => 0.97, 'postcode' => '57000', 'city' => 'Metz'],
                'geometry' => ['coordinates' => [6.1844, 49.1279]],
            ]]]),
        ]);
    }

    public function test_a_signed_in_agent_gets_suggestions(): void
    {
        $response = $this->actingAs($this->agent())->getJson('/admin/address-search?q=rue+rochambeau');

        $response->assertOk();
        $response->assertJsonPath('results.0.label', '10 Rue Rochambeau 57000 Metz');
        $response->assertJsonPath('results.0.postcode', '57000');
        $response->assertJsonPath('results.0.country', 'FR');
    }

    public function test_a_guest_is_turned_away(): void
    {
        $this->getJson('/admin/address-search?q=rue+rochambeau')->assertUnauthorized();
        Http::assertNothingSent();
    }

    public function test_a_deactivated_account_is_turned_away(): void
    {
        $agent = $this->agent();
        $agent->forceFill(['is_active' => false])->save();

        $this->actingAs($agent)->getJson('/admin/address-search?q=rue+rochambeau')->assertForbidden();
        Http::assertNothingSent();
    }

    public function test_the_query_is_required(): void
    {
        $this->actingAs($this->agent())->getJson('/admin/address-search')->assertUnprocessable();
    }

    public function test_the_provider_keys_never_reach_the_response(): void
    {
        $body = $this->actingAs($this->agent())->getJson('/admin/address-search?q=rue+rochambeau')->getContent();

        foreach (['apiKey', 'geoapify', 'locationiq', config('services.address_search.geoapify_key')] as $secret) {
            $this->assertStringNotContainsString((string) $secret, $body);
        }
    }

    private function agent(): User
    {
        return User::firstOrCreate(
            ['email' => 'agent@example.com'],
            ['name' => 'Agent', 'password' => 'password', 'role' => UserRole::Agent, 'is_active' => true],
        );
    }
}
