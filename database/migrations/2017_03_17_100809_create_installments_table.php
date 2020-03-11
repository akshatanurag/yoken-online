<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('frequency')->unsigned();
            $table->string('amounts',100);
            $table->string('payment_duration',150);
            $table->smallInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('installments', function (Blueprint $table) {
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
        Schema::dropIfExists('installments');
    }
}
