<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Database\Category\FileConstraint;

class CategoryFileConstraintsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // Animation
      $this->add('animation', 1);
      $this->add('animation', 2);
    }

    private function add($category_id, $constraint_id){
      DB::table('category_file_constraint')->insert(
        [ "category_id" => $category_id, "file_constraint_id" => $constraint_id ]
      );
    }
}
