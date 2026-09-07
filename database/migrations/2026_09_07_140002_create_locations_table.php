<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('type', 32)->default('city');
            $table->string('city', 120)->nullable();
            $table->char('country', 2)->default('FR');
            // Held here so picking a location fills the shipment's coordinates without
            // a geocoder round-trip; the shipment keeps its own copy afterwards.
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
