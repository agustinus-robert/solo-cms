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
        Schema::create('cms_menu_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->string('icon', 50);
            $table->integer('type');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cms_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->integer('status');
            $table->longText('description');
            $table->unsignedBigInteger('parent');
            $table->string('location', 255);
            $table->longText('image');
            $table->unsignedBigInteger('id_menu_category');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_menu_category')->references('id')->on('cms_menu_category')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_menu_category');
        Schema::dropIfExists('cms_category');
    }
};
