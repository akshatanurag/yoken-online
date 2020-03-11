<?php
/**
 * Created by PhpStorm.
 * User: aman
 * Date: 3/14/17
 * Time: 12:02 AM
 */
use Illuminate\Database\Seeder;
use \App\Course;
class CoursesTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker\Factory::create();

        foreach(range(1,30) as $i)
        {
            $institute = \App\Institute::orderByRaw("RAND()")->first();
            Course::create([
                'institute_id' => $institute -> id,
                'name' => $faker->word,
                'description' => $faker->paragraph(4),
                'demo_classes' => $faker->numberBetween(0,7),
                'syllabus' => $faker->paragraph(10),
                'classes_per_week' => $faker->numberBetween(0,7),
                'fees' => $faker->numberBetween(3000,15000),
                'discount' => $faker->numberBetween(0,7),
                'duration' => $faker->numberBetween(0,7),
                'duration_type' => $faker->word,
                'pic_link' => $faker->imageUrl(300,200),
            ]);
        }
    }

}