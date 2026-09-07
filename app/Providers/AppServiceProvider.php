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

        FilamentAsset::register([
            Css::make('leaflet-css')->html(
                '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" '
                .'integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />'
            ),
            Js::make('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js')
                ->extraAttributes([
                    'integrity' => 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=',
                    'crossorigin' => '',
                ]),
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
