<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Traits\Metable\MetableSchema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('ref_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('ref_provinces')->onDelete('cascade');
            $table->string('name');
            $table->string('postal_code');
            $table->timestamps();
        });

        Schema::create('ref_districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('ref_cities')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_provinces');
        Schema::dropIfExists('ref_cities');
        Schema::dropIfExists('ref_districts');
    }
};
