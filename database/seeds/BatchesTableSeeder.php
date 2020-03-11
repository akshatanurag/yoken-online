<?php

/**
 * Created by PhpStorm.
 * User: aman
 * Date: 3/14/17
 * Time: 10:56 AM
 */
use \Illuminate\Database\Seeder;

class BatchesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();
        foreach(range(1,30) as $index) {
            $course = \App\Course::orderByRaw('RAND()')->first();
            \App\Batch::create([
                'course_id' => $course->id,
                'no_of_seats' => $faker->numberBetween(10,100),
                'commence_date' => $faker->date('d-m-Y'),
                'timings' => $faker->sentence(),
                'days' => $faker->sentence()
            ]);
        }
    }
}