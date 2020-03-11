<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebinarPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('webinar_registration_id')->unsigned();
            $table->string('payment_id', 200);
            $table->string('payment_status', 20);
            $table->longText('payment_details');
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
        Schema::table('webinar_payments', function (Blueprint $table) {
           $table->foreign('webinar_registration_id')
               ->references('id')
               ->on('webinar_registrations')
               ->onDelete('cascade');
        });
    }
}
