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
      
        $this->call(UserSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(FileConstraintsSeeder::class);
        $this->call(CategoryFileConstraintsSeeder::class);
    }
}
