<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('tour_package_id')->constrained('tour_packages');

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            $table->integer('qty_person');
            $table->date('schedule_date');
            $table->decimal('total_amount', 15, 2);

            $table->string('status')->default('pending');
            $table->string('payment_gateway')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('payment_reference')->nullable();

            $table->text('payment_url')->nullable();
            $table->json('payload_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_orders');
    }
};
