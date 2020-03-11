<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',11);
            $table->string('discount_type',10);
            $table->float('discount_value');
            $table->integer('allowed_per_user');
            //created_by: Either 'YOKEN' or institute_id
            $table->string('created_by');
            $table->string('target_type');
            $table->integer('target_value');
            $table->timestamp('expire_timestamp');
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
        Schema::dropIfExists('coupons');
    }
}
