<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstitutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description', 2000);
            $table->string('state', 20);
            $table->string('city', 20);
            $table->string('location', 20);
            $table->string('email', 50)->unique();
            $table->string('contact', 15);
            $table->string('address', 120);
            $table->string('affiliation', 20);
            $table->integer('no_of_students');
            $table->string('logo_file',200);
            $table->string('display_pic_links',1000);
            $table->string('password',100);
            $table->tinyInteger('status')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('institutes');
    }
}
