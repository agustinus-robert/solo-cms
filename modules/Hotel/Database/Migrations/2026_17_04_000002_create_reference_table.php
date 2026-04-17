<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Referensi Fasilitas (misal: WiFi, AC, Breakfast, Kolam Renang)
        Schema::create('hotel_ref_amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable(); // Simpan class font-awesome atau path svg
            $table->timestamps();
        });

        // Tabel pivot antara Tipe Kamar dan Fasilitas
        Schema::create('hotel_room_type_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('hotel_room_types')->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained('hotel_ref_amenities')->onDelete('cascade');
        });

        // Referensi Sumber Reservasi (Booking Source)
        Schema::create('hotel_ref_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Traveloka, Booking.com, Direct, Website
            $table->decimal('commission_rate', 5, 2)->default(0); // Jika ada potongan komisi
            $table->timestamps();
        });

        // Menambahkan kolom source_id ke tabel booking yang sudah ada
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->foreignId('source_id')->nullable()->after('room_id')->constrained('hotel_ref_sources');
        });
    }

    public function down(): void
    {
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
            $table->dropColumn('source_id');
        });
        Schema::dropIfExists('hotel_room_type_amenities');
        Schema::dropIfExists('hotel_ref_sources');
        Schema::dropIfExists('hotel_ref_amenities');
    }
};
