<?php

/**
 * Created by PhpStorm.
 * User: aman
 * Date: 3/14/17
 * Time: 11:09 AM
 */
use Illuminate\Database\Seeder;
class FacultiesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();
        foreach(range(1,30) as $index) {
            $course = \App\Course::orderByRaw('RAND()')->first();
            \App\Faculty::create([
                'course_id' => $course->id,
                'name' => $faker->name,
                'description' => $faker->paragraph(4),
                'experience' => $faker->numberBetween(1,40),
                'speciality' => $faker->sentence(2),
                'pic_link' => $faker->imageUrl(400,400)
            ]);
        }
    }
}