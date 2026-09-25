<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
use App\Filament\Pages\Settings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_removed_sections_are_gone(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(Settings::class)
            ->assertOk()
            ->assertDontSee('Position sur la carte')
            ->assertDontSee('Identité légale')
            ->assertDontSee('Hébergeur');
    }

    public function test_saving_pins_the_map_on_the_geocoded_address(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '43.2965', 'lon' => '5.3698']])]);
        $this->actingAs($this->admin());

        Livewire::test(Settings::class)
            ->set('data.address', '1 La Canebière, 13001 Marseille')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('43.2965', Setting::values()['map_lat']);
        $this->assertSame('5.3698', Setting::values()['map_lng']);
    }

    public function test_an_address_that_cannot_be_geocoded_clears_the_pin(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([])]);
        Setting::putMany(['map_lat' => '48.8566', 'map_lng' => '2.3522']);
        $this->actingAs($this->admin());

        Livewire::test(Settings::class)
            ->set('data.address', 'Adresse à confirmer')
            ->call('save');

        $this->assertNull(Setting::values()['map_lat']);
        $this->assertNull(Setting::values()['map_lng']);
    }

    public function test_the_legal_notice_names_the_company_from_the_contact_details(): void
    {
        config(['brand.contact.address' => '1 La Canebière, 13001 Marseille']);

        $this->get(route('legal.notice', ['locale' => 'fr']))
            ->assertOk()
            ->assertSee(config('app.name'))
            ->assertSee('1 La Canebière, 13001 Marseille')
            ->assertDontSee('Forme juridique');
    }

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'role' => UserRole::Admin, 'is_active' => true],
        );
    }
}
