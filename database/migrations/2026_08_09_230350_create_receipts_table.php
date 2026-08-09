<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained();
            $table->string('number', 32)->unique();
            $table->timestamp('issued_at');
            $table->decimal('total_ht', 12, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->decimal('total_ttc', 12, 2);
            $table->char('currency', 3)->default('EUR');
            $table->string('path', 255);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            $table->index('shipment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
