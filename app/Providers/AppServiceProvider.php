<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\Geocoding\GeocoderProvider;
use App\Services\Geocoding\NominatimGeocoder;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GeocoderProvider::class, function () {
            $provider = config('services.geocoder.provider');

            return match ($provider) {
                'nominatim' => new NominatimGeocoder(config('services.geocoder.user_agent') ?? ''),
                default => throw new RuntimeException("Unsupported geocoder provider [{$provider}]."),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->overlayBrandSettings();

        // Self-hosted: a blocked or unreachable CDN left the map a blank box with no
        // error anywhere in the UI.
        FilamentAsset::register([
            Css::make('leaflet-css')->html(
                '<link rel="stylesheet" href="'.asset('vendor/leaflet/leaflet.css').'" />'
            ),
            Js::make('leaflet-js', asset('vendor/leaflet/leaflet.js')),
        ]);
    }

    /**
     * Anything the admin has filled in on the Settings page wins over the config
     * placeholders, so the 18 config('brand.contact.*') call sites stay untouched.
     */
    private function overlayBrandSettings(): void
    {
        $stored = collect(Setting::values())
            ->filter(fn (?string $value) => filled($value))
            ->all();

        if ($stored === []) {
            return;
        }

        config(['brand.contact' => [...config('brand.contact'), ...$stored]]);
    }
}
