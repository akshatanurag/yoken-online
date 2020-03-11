<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('batch_id')->unsigned();
            //base_fees & base_discount are set by the institute
            $table->float('base_fees');
            $table->float('base_discount');
            $table->boolean('one_time');
            $table->boolean('type');
            $table->integer('installment_id')->unsigned()->nullable();
            $table->string('yoken_promo_code', 20)->nullable();
            $table->float('yoken_rebate')->nullable();
            $table->string('institute_promo_code', 20)->nullable();
            $table->float('institute_rebate')->nullable();
            $table->unsignedTinyInteger('installment_index');
            $table->timestamps();
        });

        Schema::table('enrollments', function (Blueprint $table) {
           $table->foreign('user_id')
               ->references('id')
               ->on('users')
               ->onDelete('cascade');
            $table->foreign('batch_id')
                ->references('id')
                ->on('batches')
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
        Schema::dropIfExists('enrollments');
    }
}
