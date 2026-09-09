<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** The four values the enum used to hold, as the rows the office starts from. */
    private const SEED = [
        'virement' => 'Virement bancaire',
        'especes' => 'Espèces',
        'carte' => 'Carte bancaire',
        'credit' => 'Compte crédit',
    ];

    public function up(): void
    {
        Schema::create('payment_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        foreach (self::SEED as $name) {
            DB::table('payment_modes')->insert(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('payment_mode_id')->nullable()->after('payment_mode')->constrained()->nullOnDelete();
        });

        // payment_mode held the enum's slug; it now keeps the name at booking time, the
        // same shadowing as carrier_name and shipment_mode.
        foreach (self::SEED as $slug => $name) {
            $id = DB::table('payment_modes')->where('name', $name)->value('id');

            DB::table('shipments')->where('payment_mode', $slug)
                ->update(['payment_mode' => $name, 'payment_mode_id' => $id]);
        }
    }

    public function down(): void
    {
        foreach (self::SEED as $slug => $name) {
            DB::table('shipments')->where('payment_mode', $name)->update(['payment_mode' => $slug]);
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_mode_id');
        });

        Schema::dropIfExists('payment_modes');
    }
};
