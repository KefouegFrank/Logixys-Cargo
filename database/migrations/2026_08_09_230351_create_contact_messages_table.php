<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150);
            $table->string('phone', 40)->nullable();
            $table->string('service_type', 32)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('message');
            $table->char('locale', 2)->default('fr');
            $table->boolean('is_handled')->default(false);
            $table->foreignId('handled_by')->nullable()->constrained('users');
            $table->timestamp('consent_at');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
