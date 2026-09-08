<?php

namespace Database\Seeders;

use App\Enums\LocationType;
use App\Models\Carrier;
use App\Models\Location;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    // Fills the carrier / location / customer pickers on the shipment form. The lanes in
    // ShipmentSeeder reuse these same places.
    public function run(): void
    {
        foreach ([
            ['name' => 'Logixys Route', 'code' => 'LGX-R', 'contact_email' => 'route@logixyscargo.fr'],
            ['name' => 'Air France Cargo', 'code' => 'AFC', 'contact_email' => 'ops@af-cargo.example'],
            ['name' => 'CMA CGM', 'code' => 'CMA', 'contact_email' => 'booking@cma.example'],
            ['name' => 'Geodis', 'code' => 'GEO', 'contact_email' => 'contact@geodis.example'],
        ] as $carrier) {
            Carrier::updateOrCreate(['code' => $carrier['code']], $carrier);
        }

        foreach ([
            ['name' => 'Paris', 'type' => LocationType::City, 'city' => 'Paris', 'country' => 'FR', 'lat' => 48.8566, 'lng' => 2.3522],
            ['name' => 'Lyon', 'type' => LocationType::City, 'city' => 'Lyon', 'country' => 'FR', 'lat' => 45.7640, 'lng' => 4.8357],
            ['name' => 'Paris CDG', 'type' => LocationType::Airport, 'city' => 'Roissy', 'country' => 'FR', 'lat' => 49.0097, 'lng' => 2.5479],
            ['name' => 'New York JFK', 'type' => LocationType::Airport, 'city' => 'New York', 'country' => 'US', 'lat' => 40.6413, 'lng' => -73.7781],
            ['name' => 'Port du Havre', 'type' => LocationType::Port, 'city' => 'Le Havre', 'country' => 'FR', 'lat' => 49.4944, 'lng' => 0.1079],
            ['name' => 'Port de Douala', 'type' => LocationType::Port, 'city' => 'Douala', 'country' => 'CM', 'lat' => 4.0511, 'lng' => 9.7679],
            ['name' => 'Port de Marseille', 'type' => LocationType::Port, 'city' => 'Marseille', 'country' => 'FR', 'lat' => 43.2965, 'lng' => 5.3698],
            ['name' => 'Entrepôt Logixys', 'type' => LocationType::Warehouse, 'city' => 'Paris', 'country' => 'FR', 'lat' => 48.8300, 'lng' => 2.3700],
        ] as $location) {
            Location::updateOrCreate(['name' => $location['name']], $location);
        }

    }
}
