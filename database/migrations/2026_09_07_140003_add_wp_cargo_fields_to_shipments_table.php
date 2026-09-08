<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('carrier_id')->nullable()->after('carrier_reference')->constrained()->nullOnDelete();

            // The *_label and *_lat/lng columns stay authoritative: a shipment keeps the
            // address it was booked with even if the location record is later edited.
            $table->foreignId('origin_location_id')->nullable()->after('origin_label')->constrained('locations')->nullOnDelete();
            $table->foreignId('destination_location_id')->nullable()->after('destination_label')->constrained('locations')->nullOnDelete();

            $table->time('pickup_time')->nullable()->after('pickup_date');
            $table->time('departure_time')->nullable()->after('pickup_time');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('carrier_id');
            $table->dropConstrainedForeignId('origin_location_id');
            $table->dropConstrainedForeignId('destination_location_id');
            $table->dropColumn(['pickup_time', 'departure_time']);
        });
    }
};
