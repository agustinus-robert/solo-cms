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
        Schema::create('product_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->tinyInteger('payment_on');
            $table->tinyInteger('status')->nullable();
            $table->string('comments')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('product_quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_quotation_id')->constrained('product_quotations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 20, 2);
            $table->tinyInteger('status')->nullable();
            $table->text('location')->nullable();
            $table->text('image_name')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('outlet_product_quotations', function (Blueprint $table){
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('quotation_id')->constrained('product_quotations')->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }
};
