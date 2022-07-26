<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCycleCountingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycle_countings', function (Blueprint $table) {
            $table->id();
            $table->integer('schedule_id');
            $table->bigInteger('actual_count');
            $table->bigInteger('recorded_count');
            $table->bigInteger('variance');
            $table->double('inv_rec_accuracy');
            $table->enum('status', ['PENDING', 'COMPLETED', 'REJECTED']);
            $table->string('approve_before');
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
        Schema::dropIfExists('cycle_countings');
    }
}
