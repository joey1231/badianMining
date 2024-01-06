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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->string('name');
            $table->string('position')->default('');
            $table->double('daily_salary')->default(400);
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('worker_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->timestamp('date');
            $table->bigInteger('worker_id')->unsigned()->index();
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('status')->default(1); //0 - absent, 1 - present, 2, half-day, 4: late
            $table->timestamps();
        });
        Schema::create('payrols', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->timestamp('date_from');
            $table->timestamp('date_to');
            $table->double('total_amount')->default(0);
            $table->string('paid_status')->default('unpaid');
            $table->timestamps();
        });
        Schema::create('payrol_item', function (Blueprint $table) {
            $table->id();
            $table->string('hash_id')->index();
            $table->bigInteger('worker_id')->unsigned()->index();
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade')->onUpdate('cascade');
            $table->double('total_amount')->default(0);
            $table->string('paid_status')->default('unpaid');
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
        Schema::dropIfExists('payrol_item');
        Schema::dropIfExists('payrols');
        Schema::dropIfExists('worker_attendances');
        Schema::dropIfExists('workers');
    }
};
