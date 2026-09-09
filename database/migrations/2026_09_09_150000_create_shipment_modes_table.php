<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** The three values the enum used to hold, as the rows the office starts from. */
    private const SEED = [
        'door_to_door' => 'Porte à porte',
        'door_to_port' => 'Porte à port',
        'port_to_port' => 'Port à port',
    ];

    public function up(): void
    {
        Schema::create('shipment_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        foreach (self::SEED as $name) {
            DB::table('shipment_modes')->insert(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('shipment_mode_id')->nullable()->after('shipment_mode')->constrained()->nullOnDelete();
        });

        // shipment_mode kept the enum's slug; it now holds the mode's name at booking
        // time, the same way carrier_name shadows carrier_id.
        foreach (self::SEED as $slug => $name) {
            $id = DB::table('shipment_modes')->where('name', $name)->value('id');

            DB::table('shipments')->where('shipment_mode', $slug)
                ->update(['shipment_mode' => $name, 'shipment_mode_id' => $id]);
        }
    }

    public function down(): void
    {
        foreach (self::SEED as $slug => $name) {
            DB::table('shipments')->where('shipment_mode', $name)->update(['shipment_mode' => $slug]);
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipment_mode_id');
        });

        Schema::dropIfExists('shipment_modes');
    }
};
