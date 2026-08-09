<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 20)->unique();
            $table->string('status', 32);
            $table->string('service_type', 32);
            $table->string('shipment_mode', 32);
            $table->string('carrier_name', 120)->nullable();
            $table->string('carrier_reference', 120)->nullable();
            $table->char('locale', 2)->default('fr');

            $table->string('shipper_name', 150);
            $table->string('shipper_company', 150)->nullable();
            $table->string('shipper_email', 150)->nullable();
            $table->string('shipper_phone', 40)->nullable();
            $table->string('shipper_address', 255)->nullable();
            $table->string('shipper_postcode', 20)->nullable();
            $table->string('shipper_city', 120);
            $table->char('shipper_country', 2)->default('FR');

            $table->string('receiver_name', 150);
            $table->string('receiver_company', 150)->nullable();
            $table->string('receiver_email', 150)->nullable();
            $table->string('receiver_phone', 40)->nullable();
            $table->string('receiver_address', 255)->nullable();
            $table->string('receiver_postcode', 20)->nullable();
            $table->string('receiver_city', 120);
            $table->char('receiver_country', 2);

            $table->string('origin_label', 255);
            $table->decimal('origin_lat', 10, 7)->nullable();
            $table->decimal('origin_lng', 10, 7)->nullable();
            $table->string('destination_label', 255);
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();
            $table->integer('distance_km')->nullable();

            $table->date('pickup_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->text('goods_description')->nullable();
            $table->integer('package_count')->default(0);
            $table->decimal('total_weight_kg', 10, 2)->default(0);
            $table->decimal('total_volume_cbm', 10, 3)->default(0);
            $table->decimal('declared_value', 12, 2)->nullable();
            $table->char('currency', 3)->default('EUR');

            $table->decimal('freight_cost', 12, 2)->default(0);
            $table->decimal('insurance_cost', 12, 2)->default(0);
            $table->decimal('customs_cost', 12, 2)->default(0);
            $table->decimal('other_cost', 12, 2)->default(0);
            $table->decimal('total_ht', 12, 2)->default(0);
            // tax_rate/tax_label/tax_exemption_note: jurisdiction-agnostic naming per section 6
            // prose and section 17 env defaults, not the vat_* columns in the section 6 table.
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->string('tax_label', 32)->default('TVA');
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->text('tax_exemption_note')->nullable();
            $table->decimal('total_ttc', 12, 2)->default(0);
            $table->string('payment_mode', 32)->nullable();
            $table->string('payment_status', 16)->default('unpaid');

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
            $table->index('receiver_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
