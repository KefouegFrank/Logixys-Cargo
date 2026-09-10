<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Origine and Destination are the countries a shipment runs between; the address of
     * the goods right now lives on each tracking event instead.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->char('origin_country', 2)->nullable()->after('origin_label');
            $table->char('destination_country', 2)->nullable()->after('destination_label');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['origin_country', 'destination_country']);
        });
    }
};
