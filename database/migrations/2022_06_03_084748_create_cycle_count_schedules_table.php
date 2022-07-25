<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCycleCountSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycle_count_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('sku_id');
            $table->string('schedule');
            $table->integer('staff_id');
            $table->enum('status', ['OPEN', 'CLOSED']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cycle_count_schedules');
    }
}
