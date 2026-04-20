<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_ref_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('type')->default(1);
            $table->integer('total_stock')->default(0);
            $table->string('unit')->default('pcs');
            $table->integer('min_stock')->default(5);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_room_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('hotel_rooms')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('hotel_ref_inventories')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_ref_inventories');
        Schema::dropIfExists('hotel_room_inventories');
    }
};
