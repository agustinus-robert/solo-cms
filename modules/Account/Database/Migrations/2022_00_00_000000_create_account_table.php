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
        DB::statement("ALTER SEQUENCE users_id_seq RESTART WITH 10000001;");

        Schema::create('user_password_resets', function (Blueprint $table) {
            $table->string('token');
            $table->string('email');
            $table->integer('expired_in')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('email');
        });

        Schema::create('user_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('address')->unique();
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('user_phones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('number')->unique();
            $table->boolean('whatsapp')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('user_profile', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');

            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('pob')->nullable();
            $table->date('dob')->nullable();
            $table->smallInteger('sex')->nullable();
            $table->boolean('is_dead')->default(false);

            $table->string('avatar')->nullable();
            $table->timestamps();

            $table->primary('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->index('name');
            $table->index('sex');
            $table->index('phone');
        });

        Schema::create('user_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('user_address', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('village')->nullable();

            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();

            $table->string('postal', 10)->nullable();
            $table->boolean('is_main')->default(false);
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('ref_provinces')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('ref_cities')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('ref_districts')->onDelete('set null');
        });

        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('token');
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('permission_name'); // Relasi ke permission Spatie
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
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
        // Schema::dropIfExists('user_password_resets');
        // Schema::dropIfExists('user_logs');
        // Schema::dropIfExists('user_meta');
        // Schema::dropIfExists('users');
    }
};
