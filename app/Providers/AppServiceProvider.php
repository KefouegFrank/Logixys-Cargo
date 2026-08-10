<?php

namespace App\Providers;

use App\Services\Geocoding\GeocoderProvider;
use App\Services\Geocoding\NominatimGeocoder;
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
        //
    }
}
