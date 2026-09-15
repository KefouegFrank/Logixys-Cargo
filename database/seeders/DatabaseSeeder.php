<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * The first admin account isn't seeded — see `php artisan app:make-admin`. Run that
     * (or create one from the panel) before this, since ShipmentSeeder attributes its demo
     * rows to whichever user already exists.
     */
    public function run(): void
    {
        $this->call([
            ReferenceDataSeeder::class,
            ShipmentSeeder::class,
        ]);
    }
}
