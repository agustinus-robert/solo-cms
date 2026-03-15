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
        Schema::create('cms_post', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_id');
            $table->longText('content');
            $table->longText('tags')->nullable();
            $table->longText('location')->nullable();
            $table->longText('image')->nullable();
            $table->integer('status');
            $table->longText('alt_image')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('menu_id')->references('id')->on('cms_menu')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('cms_post_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cms_post_id');
            $table->string('type', 50);
            $table->string('key', 255);
            $table->longText('value');

            $table->foreign('cms_post_id')->references('id')->on('cms_post')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('cms_schedule_post', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cms_post_id');
            $table->date('schedule_on');
            $table->time('timepicker');

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('cms_post_id')->references('id')->on('cms_post')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('cms_post_image', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('post_id');
            $table->longText('title');
            $table->longText('slug');
            $table->string('location', 255);
            $table->longText('image');
            $table->longText('content');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('cms_menu')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('cms_post')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('cms_post_has_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('tags_id');
            $table->string('parameter', 255);

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('cms_post')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('cms_post_image_has_category', function (Blueprint $table) {
            $table->unsignedBigInteger('id_image');
            $table->unsignedBigInteger('id_category_image');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cms_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('comment_id');
            $table->string('username', 255);
            $table->string('email', 255);
            $table->string('title', 255);
            $table->longText('description');
            $table->longText('notes');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('cms_post')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('cms_post_site_configuration', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site_name');
            $table->longText('location');
            $table->string('email', 255);
            $table->string('call', 20);
            $table->longText('coordinate');
            $table->string('twitter');
            $table->string('facebook');
            $table->string('instagram');
            $table->string('skype');
            $table->string('linkedin');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cms_post_video', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('post_id');
            $table->longText('title');
            $table->longText('slug');
            $table->longText('deskripsi');
            $table->longText('link_embed');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('cms_menu')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('cms_post')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_post');
        Schema::dropIfExists('cms_post_meta');
        Schema::dropIfExists('cms_schedule_post');
        Schema::dropIfExists('cms_post_image');
        Schema::dropIfExists('cms_post_has_category');
        Schema::dropIfExists('cms_post_image_has_category');
        Schema::dropIfExists('cms_comments');
        Schema::dropIfExists('cms_post_site_configuration');
        Schema::dropIfExists('cms_post_video');
    }
};
