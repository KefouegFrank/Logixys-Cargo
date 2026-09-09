<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ville and Pays left the booking form: the address line carries them, and the leg
     * itself is the Origine / Destination pair. Existing rows keep what they have.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('shipper_city', 120)->nullable()->change();
            $table->char('shipper_country', 2)->nullable()->default(null)->change();
            $table->string('receiver_city', 120)->nullable()->change();
            $table->char('receiver_country', 2)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('shipper_city', 120)->nullable(false)->change();
            $table->char('shipper_country', 2)->default('FR')->nullable(false)->change();
            $table->string('receiver_city', 120)->nullable(false)->change();
            $table->char('receiver_country', 2)->nullable(false)->change();
        });
    }
};
