<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id')->unsigned();
            $table->string('name', 50);
            $table->string('description', 2000);
            $table->string('syllabus', 5000);
            $table->string('pic_link', 200);
            $table->integer('demo_classes');
            $table->integer('classes_per_week');
            $table->integer('fees');
            $table->integer('discount');
            $table->float('duration', 4, 2);
            $table->string('duration_type');
            $table->smallInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('courses', function($table) {
           $table->foreign('institute_id')
                ->references('id')
                ->on('institutes')
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('courses');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
