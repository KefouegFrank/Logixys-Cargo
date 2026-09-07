<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\Geocoding\GeocoderProvider;
use App\Services\Geocoding\NominatimGeocoder;
use Filament\Forms\Components\Select;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Filters\SelectFilter;
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

        // Native <select> popups are destroyed by any Livewire re-render, so a dropdown
        // shuts the moment you open it. Filament's own is Alpine-driven and survives one.
        // SelectFilter covers TernaryFilter too: configuration walks the class hierarchy.
        Select::configureUsing(fn (Select $select) => $select->native(false));
        SelectFilter::configureUsing(fn (SelectFilter $filter) => $filter->native(false));

        // Self-hosted: a blocked or unreachable CDN left the map a blank box with no
        // error anywhere in the UI.
        FilamentAsset::register([
            // Passed as paths, not raw HTML: an asset with a null path makes
            // `filament:upgrade` fail when it tries to copy it.
            Css::make('leaflet-css', asset('vendor/leaflet/leaflet.css')),
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
