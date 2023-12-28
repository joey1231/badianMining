<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('role');
            $table->string('hash_id')->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('hash_id')->index();
            $table->string('permission');
            $table->rememberToken();
            $table->timestamps();
        });

        // role belongsToMany permissions
        Schema::create('role_permission', function (Blueprint $table) {

            $table->bigInteger('role_id')->unsigned()->index();
            $table->bigInteger('permission_id')->unsigned()->index();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash_id')->index();
            $table->string('name');
            $table->double('adviser_comm_percent')->default(90);
            $table->double('company_comm_percent')->default(10);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

        });
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash_id')->index();
            $table->string('name');
            $table->boolean('owner');
            $table->string('username')->unique();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->bigInteger('company_id')->unsigned()->index();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->rememberToken();
            $table->tinyInteger('is_owner')->default(0);
            $table->boolean('full_access')->default(false);
            $table->integer('type_of_access')->default(0);
            $table->boolean('revoke_access')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // user belongsToMany role
        Schema::create('user_role', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index();
            $table->bigInteger('role_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('users');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
