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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('location');
            $table->text('overview');
            $table->string('opening_hours')->nullable();
            $table->decimal('base_price', 15, 2)->default(0);
            $table->json('highlights')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Tambah ini
        });

        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->string('package_name');
            $table->decimal('price_per_person', 15, 2);
            $table->json('labels')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Tambah ini
        });

        Schema::create('tour_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Tambah ini
        });

        // Tabel pivot tour_package_label biasanya tidak butuh softDeletes
        // karena fungsinya hanya penghubung (M-to-M).
        Schema::create('tour_package_label', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('tour_label_id')->constrained()->onDelete('cascade');
        });

        Schema::create('tour_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
            $table->date('available_date');
            $table->integer('stock')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Tambah ini
        });

        Schema::create('tour_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->text('content');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes(); // Tambah ini
        });

        Schema::create('tour_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes(); // Tambah ini
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_photos');
        Schema::dropIfExists('tour_details');
        Schema::dropIfExists('tour_availabilities');
        Schema::dropIfExists('tour_package_label');
        Schema::dropIfExists('tour_labels');
        Schema::dropIfExists('tour_packages');
        Schema::dropIfExists('tours');
    }
};
