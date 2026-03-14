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
        Schema::create('cms_menu', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255);
            $table->string('icon', 50);
            $table->string('slug', 255);
            $table->integer('type');
            $table->longText('meta');
            $table->string('custom_links', 255);
            $table->longText('post_code');
            $table->longText('taxonomy_code');
            $table->longText('image_code');
            $table->longText('woocomerce_code');
            $table->longText('meta_keyword');
            $table->tinyInteger('add')->nullable();
            $table->tinyInteger('edit')->nullable();
            $table->tinyInteger('delete')->nullable();
            $table->tinyInteger('album')->nullable();
            $table->tinyInteger('video')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cms_menu_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('menu_text');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_menu');
        Schema::dropIfExists('cms_menu_order');
    }
};
