<?php

/**
 * Created by PhpStorm.
 * User: aman
 * Date: 3/16/17
 * Time: 2:52 PM
 */
use \Illuminate\Database\Seeder;
class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();
        foreach(range(1,10) as $i) {
            \App\Category::create([
                'name' => $faker->word
            ]);
        }
    }
}