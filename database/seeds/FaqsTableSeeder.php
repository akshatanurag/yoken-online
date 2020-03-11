<?php
/**
 * Created by: Amandeep.
 * For: YokenOnline
 * Date: 3/24/17
 * Time: 12:55 AM
 */
use Illuminate\Database\Seeder;
class FaqsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();
        foreach(range(1,30) as $index) {
            $course = \App\Course::orderByRaw('RAND()')->first();
            \App\Faq::create([
                'course_id' => $course->id,
                'question' => $faker->paragraph(2),
                'answer' => $faker->paragraph(4),
            ]);
        }
    }
}