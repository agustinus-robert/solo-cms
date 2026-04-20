<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotel_room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('category')->default(1);
            $table->integer('capacity')->nullable();
            $table->decimal('base_price', 12, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('hotel_room_types')->onDelete('cascade');
            $table->string('room_number')->unique();
            $table->integer('floor');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('hotel_guests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('id_card_number')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('hotel_guests')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('hotel_rooms')->onDelete('cascade');
            $table->dateTime('check_in_plan');
            $table->dateTime('check_out_plan');
            $table->dateTime('actual_check_in')->nullable();
            $table->dateTime('actual_check_out')->nullable();
            $table->decimal('total_price', 12, 2);
            $table->integer('status')->default(1);
            $table->integer('payment_status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_additional_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('hotel_bookings')->onDelete('cascade');
            $table->string('service_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_additional_services');
        Schema::dropIfExists('hotel_bookings');
        Schema::dropIfExists('hotel_guests');
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotel_room_types');
    }
};
