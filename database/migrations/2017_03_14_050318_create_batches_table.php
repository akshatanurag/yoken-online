<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('no_of_seats')->unsigned();
            $table->string('commence_date',15);
            $table->string('timings');
            $table->string('days');
            $table->smallInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::table('batches', function (BluePrint $table) {
           $table->foreign('course_id')
               ->references('id')
               ->on('courses')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batches');
    }
}
