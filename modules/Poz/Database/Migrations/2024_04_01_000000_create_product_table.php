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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->integer('alert_qty')->nullable();
            $table->string('code', 30)->nullable();
            $table->string('name', 100);
            $table->string('barcode', 50)->nullable();
            $table->foreignId('brand_id')->nullable()->constrained('ref_brands')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('ref_categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('ref_categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('ref_units')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('tax_rate_id')->nullable()->constrained('ref_tax_rates')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('price', 20, 2);
            $table->text('description')->nullable();
            $table->text('location')->nullable();
            $table->text('image_name')->nullable();
            $table->decimal('wholesale', 20, 2);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('product_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('meta_key')->index();
            $table->text('meta_value')->nullable();

            $table->timestampsTz();
        });

        Schema::create('product_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('name');
            $table->tinyInteger('type');
            $table->json('config');
            $table->dateTimeTz('start_date');
            $table->dateTimeTz('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });

        Schema::create('product_label_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->jsonb('attributes');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('product_master_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code', 10);
            $table->string('name', 100);
            $table->decimal('price', 20, 2);
            $table->integer('qty');
            $table->integer('alert_qty');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('product_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('meta_key')->index();
            $table->text('meta_value')->nullable();

            $table->timestampsTz();
        });

        Schema::create('product_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_label_variants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('location')->nullable();
            $table->text('image_name')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });


        Schema::create('product_carts', function (Blueprint $table) {
            $table->id();
            $table->text('session_id')->nullable();
            $table->text('num')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('qty');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_label_variants');
        Schema::dropIfExists('product_master_variants');
        Schema::dropIfExists('product_galleries');
    }
};
