<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Traits\Metable\MetableSchema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmp_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kd');
            $table->string('name')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->unique('kd');

            $table->index('name');
        });

        Schema::create('cmp_role_permissions', function (Blueprint $table) {
            $table->unsignedSmallInteger('role_id');
            $table->unsignedSmallInteger('permission_id');

            $table->foreign('role_id')->references('id')->on('cmp_roles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('app_permissions')->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('cmp_role_users', function (Blueprint $table) {
            $table->unsignedSmallInteger('role_id');
            $table->unsignedInteger('user_id');

            $table->foreign('role_id')->references('id')->on('cmp_roles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cmp_role_users');
        Schema::dropIfExists('cmp_role_permissions');
        Schema::dropIfExists('cmp_roles');
    }
};
