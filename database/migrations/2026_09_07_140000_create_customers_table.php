<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('company', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('city', 120)->nullable();
            $table->char('country', 2)->default('FR');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
