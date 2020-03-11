<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebinarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);
            $table->string('image_url', 100);
            $table->string('description', 800);
            $table->timestamp('starts_at')->default(DB::raw('CURRENT_TIMESTAMP'));;
            $table->timestamp('ends_at')->default(DB::raw('CURRENT_TIMESTAMP'));;
            $table->string('room_url', 100);
            $table->float('fees', 8, 2);
            $table->float('discount', 6, 2);
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
        Schema::dropIfExists('webinars');
    }
}
