<?php

/**
 * Created by PhpStorm.
 * User: aman
 * Date: 3/14/17
 * Time: 12:34 AM
 */
use Illuminate\Database\Seeder;
use App\Institute;
class InstitutesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();
        foreach(range(1,30) as $i) {
            Institute::create([
                'name' => $faker->word,
                'description' => $faker->paragraph(5),
                'state' => $faker->word,
                'city' => $faker->word,
                'location' => $faker->word,
                'email' => $faker->email,
                'contact' => $faker->numberBetween(9000000000,9999999999),
                'address' => $faker->sentence,
                'affiliation' => $faker->word,
                'no_of_students' => $faker->numberBetween(1, 10000),
                'logo_file' => $faker->imageUrl(300, 200),
                'display_pic_links' => $faker->sentence,
                'password' => $faker->password,
            ]);
        }
    }

}