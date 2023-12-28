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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->string('name');
            $table->double('sale_price')->default(0);

            $table->double('total_kg')->default(0);
            $table->double('total_amount')->default(0);
            $table->double('total_net_amount')->default(0);
            $table->double('total_expences')->default(0);
            $table->string('status')->default('Unshared');
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();

            $table->string('item_date')->nullable();
            $table->double('total_kg')->default(0);
            $table->bigInteger('sale_id')->unsigned()->index();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('sale_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->string('name');
            $table->double('total_amount')->default(0);
            $table->bigInteger('sale_id')->unsigned()->index();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sale_expenses');
        Schema::dropIfExists('sales');

    }
};
