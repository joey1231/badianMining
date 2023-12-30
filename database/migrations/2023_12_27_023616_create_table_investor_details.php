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
        Schema::create('investor_details', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->string('share_type')->default('ton');
            $table->double('share_value')->default(200);
            $table->timestamp('invest_on')->nullable();
            $table->timestamp('share_start')->nullable();
            $table->double('invest_value')->default(0);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->timestamps();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('sharings', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->string('name');
            $table->double('total_kg')->default(0);
            $table->double('total_amount')->default(0);
            $table->double('total_net_amount')->default(0);
            $table->double('total_expences')->default(0);
            $table->timestamp('share_on')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        Schema::create('sharing_items', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();

            $table->bigInteger('sale_id')->unsigned()->index();
            $table->double('total_amount')->default(0);
            $table->double('total_kg')->default(0);
            $table->bigInteger('sharing_id')->unsigned()->index();
            $table->foreign('sharing_id')->references('id')->on('sharings')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('sharing_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->string('name');
            $table->double('total_amount')->default(0);
            $table->bigInteger('sharing_id')->unsigned()->index();
            $table->foreign('sharing_id')->references('id')->on('sharings')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('shared', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->string('share_type')->default('ton');
            $table->double('share_value')->default(200);
            $table->double('amount')->default(0);
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('sharing_id')->unsigned()->index();
            $table->foreign('sharing_id')->references('id')->on('sharings')->onDelete('cascade')->onUpdate('cascade');
            $table->string('status')->default('pending');
            $table->string('payment_reference')->default('');
            $table->string('notes')->default('');
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
        Schema::dropIfExists('shared');
        Schema::dropIfExists('sharing_expenses');
        Schema::dropIfExists('sharing_items');
        Schema::dropIfExists('sharings');
        Schema::dropIfExists('investor_details');
    }
};
