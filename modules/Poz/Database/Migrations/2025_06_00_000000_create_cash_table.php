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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('casier_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('money', 20, 2);
            $table->enum('status', ['open', 'closed'])->default('closed')->after('money');
            $table->timestamp('opened_at')->nullable()->after('status');
            $table->timestamp('closed_at')->nullable()->after('opened_at');
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('cash_register_topups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('casier_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('money', 20, 2);
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('cash_register_logs', function(Blueprint $table){
            $table->id();
            $table->foreignId('cash_register_id')->constrained('cash_registers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('status', ['plus', 'minus']);
            $table->decimal('money', 20, 2);
            $table->string('log_type')->default('transaction')->after('status');
            $table->string('reason')->nullable()->after('money');
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('desk');
    }
};
