<?php

namespace Tests\Feature\Services;

use App\Services\AddressSearch\AddressSearchService;
use App\Services\AddressSearch\BanProvider;
use App\Services\AddressSearch\GeoapifyProvider;
use App\Services\AddressSearch\LocationIqProvider;
use App\Services\AddressSearch\PhotonProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AddressSearchTest extends TestCase
{
    public function test_ban_reads_the_french_address_file(): void
    {
        Http::fake(['api-adresse.data.gouv.fr/*' => Http::response([
            'features' => [[
                'properties' => ['label' => '10 Rue Rochambeau 57000 Metz', 'score' => 0.97, 'housenumber' => '10', 'street' => 'Rue Rochambeau', 'postcode' => '57000', 'city' => 'Metz'],
                'geometry' => ['coordinates' => [6.1844, 49.1279]],
            ]],
        ])]);

        $result = (new BanProvider)->search('10 rue rochambeau metz')[0];

        $this->assertSame('10 Rue Rochambeau 57000 Metz', $result->label);
        $this->assertSame('10 Rue Rochambeau', $result->line);
        $this->assertSame('57000', $result->postcode);
        $this->assertSame('Metz', $result->city);
        $this->assertSame('FR', $result->country);
        $this->assertSame(49.1279, $result->lat);
        $this->assertSame(6.1844, $result->lng);
    }

    public function test_ban_is_skipped_outside_france(): void
    {
        $this->assertTrue((new BanProvider)->supports('FR'));
        $this->assertTrue((new BanProvider)->supports(null));
        $this->assertFalse((new BanProvider)->supports('CM'));
    }

    public function test_photon_composes_a_label_from_its_parts(): void
    {
        Http::fake(['photon.komoot.io/*' => Http::response([
            'features' => [[
                'properties' => ['housenumber' => '4', 'street' => 'Rue Bonaberi', 'postcode' => '00237', 'city' => 'Douala', 'country' => 'Cameroun', 'countrycode' => 'cm'],
                'geometry' => ['coordinates' => [9.7679, 4.0511]],
            ]],
        ])]);

        $result = (new PhotonProvider)->search('bonaberi douala')[0];

        $this->assertSame('4 Rue Bonaberi, 00237 Douala, Cameroun', $result->label);
        $this->assertSame('CM', $result->country);
        $this->assertSame(4.0511, $result->lat);
    }

    public function test_geoapify_and_locationiq_stand_down_without_a_key(): void
    {
        $this->assertFalse((new GeoapifyProvider(null))->supports(null));
        $this->assertFalse((new LocationIqProvider(null))->supports(null));
        $this->assertTrue((new GeoapifyProvider('k'))->supports(null));
        $this->assertTrue((new LocationIqProvider('k'))->supports(null));
    }

    public function test_locationiq_reads_its_flat_result_list(): void
    {
        Http::fake(['api.locationiq.com/*' => Http::response([[
            'display_name' => 'Gilbert Avenue, Cincinnati, Ohio, 45207, USA',
            'lat' => '39.1373843', 'lon' => '-84.4838',
            'address' => ['house_number' => '2245', 'road' => 'Gilbert Ave', 'postcode' => '45207', 'city' => 'Cincinnati', 'country_code' => 'us'],
        ]])]);

        $result = (new LocationIqProvider('key'))->search('gilbert ave cincinnati')[0];

        $this->assertSame('2245 Gilbert Ave', $result->line);
        $this->assertSame('US', $result->country);
    }

    public function test_a_weak_ban_match_is_dropped_so_the_worldwide_result_shows(): void
    {
        Http::fake([
            // What BAN really answers for "bonaberi douala": a Breton village at 0.32.
            'api-adresse.data.gouv.fr/*' => Http::response(['features' => [[
                'properties' => ['label' => 'Doualan 35740 Pacé', 'score' => 0.32],
                'geometry' => ['coordinates' => [-1.79, 48.14]],
            ]]]),
            'photon.komoot.io/*' => Http::response(['features' => [[
                'properties' => ['name' => 'Bonaberi', 'city' => 'Douala', 'countrycode' => 'cm'],
                'geometry' => ['coordinates' => [9.66, 4.06]],
            ]]]),
        ]);

        $results = $this->service()->search('bonaberi douala');

        $this->assertCount(1, $results);
        $this->assertSame('photon', $results[0]->source);
    }

    public function test_both_keyless_providers_answer_and_their_results_merge(): void
    {
        Http::fake([
            'api-adresse.data.gouv.fr/*' => Http::response(['features' => [[
                'properties' => ['label' => 'Doualan 35740 Pacé', 'score' => 0.9],
                'geometry' => ['coordinates' => [-1.79, 48.14]],
            ]]]),
            'photon.komoot.io/*' => Http::response(['features' => [[
                'properties' => ['name' => 'Douala', 'city' => 'Douala', 'countrycode' => 'cm'],
                'geometry' => ['coordinates' => [9.70, 4.05]],
            ]]]),
        ]);

        $results = $this->service()->search('douala');

        // The agent picks between the French match and the Cameroonian one.
        $this->assertSame(['ban', 'photon'], array_map(fn ($r) => $r->source, $results));
    }

    public function test_the_metered_pair_is_left_alone_when_the_keyless_ones_answer(): void
    {
        Http::fake([
            'api-adresse.data.gouv.fr/*' => Http::response(['features' => [[
                'properties' => ['label' => 'Metz', 'score' => 0.96],
                'geometry' => ['coordinates' => [6.18, 49.12]],
            ]]]),
            'photon.komoot.io/*' => Http::response(['features' => []]),
            '*' => Http::response([], 500),
        ]);

        $this->service()->search('metz');

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'geoapify'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'locationiq'));
    }

    public function test_the_metered_pair_takes_over_when_neither_keyless_one_answers(): void
    {
        Http::fake([
            'api-adresse.data.gouv.fr/*' => Http::response(['features' => []]),
            'photon.komoot.io/*' => Http::response(['features' => []]),
            'api.geoapify.com/*' => Http::response(['features' => [[
                'properties' => ['formatted' => 'Almeria, Spain', 'city' => 'Almeria', 'country_code' => 'es', 'lat' => 36.83, 'lon' => -2.46],
            ]]]),
        ]);

        $results = $this->service()->search('almeria');

        $this->assertSame('geoapify', $results[0]->source);
    }

    public function test_the_same_place_from_two_providers_is_listed_once(): void
    {
        Http::fake([
            'api-adresse.data.gouv.fr/*' => Http::response(['features' => [[
                'properties' => ['label' => 'Lyon', 'score' => 0.96],
                'geometry' => ['coordinates' => [4.8357, 45.7640]],
            ]]]),
            'photon.komoot.io/*' => Http::response(['features' => [[
                'properties' => ['name' => 'Lyon', 'city' => 'Lyon', 'countrycode' => 'fr'],
                'geometry' => ['coordinates' => [4.8358, 45.7641]],
            ]]]),
        ]);

        $this->assertCount(1, $this->service()->search('lyon'));
    }

    public function test_a_provider_that_throws_does_not_stop_the_others(): void
    {
        Http::fake([
            'api-adresse.data.gouv.fr/*' => fn () => throw new \RuntimeException('network down'),
            'photon.komoot.io/*' => Http::response(['features' => [[
                'properties' => ['name' => 'Lyon', 'city' => 'Lyon', 'countrycode' => 'fr'],
                'geometry' => ['coordinates' => [4.83, 45.75]],
            ]]]),
        ]);

        $this->assertSame('photon', $this->service()->search('lyon')[0]->source);
    }

    public function test_a_query_shorter_than_the_minimum_never_leaves_the_app(): void
    {
        Http::fake();

        $this->assertSame([], $this->service()->search('ly'));
        Http::assertNothingSent();
    }

    public function test_the_same_prefix_is_only_looked_up_once(): void
    {
        Http::fake(['api-adresse.data.gouv.fr/*' => Http::response(['features' => [[
            'properties' => ['label' => 'Lyon', 'score' => 0.96], 'geometry' => ['coordinates' => [4.83, 45.75]],
        ]]])]);

        $service = $this->service();
        $service->search('lyon');
        $service->search('  LYON  ');

        Http::assertSentCount(1);
    }

    private function service(): AddressSearchService
    {
        return new AddressSearchService(
            primary: [new BanProvider, new PhotonProvider],
            fallback: [new GeoapifyProvider('key'), new LocationIqProvider('key')],
        );
    }
}
