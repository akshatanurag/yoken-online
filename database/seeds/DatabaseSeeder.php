<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('InstitutesTableSeeder');
        $this->call('CoursesTableSeeder');
        $this->call('FacultiesTableSeeder');
        $this->call('BatchesTableSeeder');
        $this->call('CategoriesTableSeeder');
        $this->call('FaqsTableSeeder');
    }
}
