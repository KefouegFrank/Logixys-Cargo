<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_suppressions', function (Blueprint $table) {
            $table->id();
            $table->string('email', 150)->unique();
            $table->string('reason', 32);
            $table->string('detail', 255)->nullable();
            $table->timestamp('suppressed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_suppressions');
    }
};
