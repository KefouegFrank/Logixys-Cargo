<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Quantité and the two weight roll-ups were screen-only figures; they get columns so
    // what an agent types on the form is what gets stored.
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->unsignedInteger('total_quantity')->default(0)->after('package_count');
            $table->decimal('volumetric_weight_kg', 10, 2)->default(0)->after('total_weight_kg');
            $table->decimal('chargeable_weight_kg', 10, 2)->default(0)->after('volumetric_weight_kg');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['total_quantity', 'volumetric_weight_kg', 'chargeable_weight_kg']);
        });
    }
};
