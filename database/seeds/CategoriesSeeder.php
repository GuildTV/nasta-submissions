<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Database\Category\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $mondayClosing = Carbon::create(2017, 2, 16, 19, 0, 0);
      $tuesdayClosing = Carbon::create(2017, 2, 17, 19, 0, 0);
      $wednesdayClosing = Carbon::create(2017, 2, 18, 19, 0, 0);
      $thursdayClosing = Carbon::create(2017, 2, 19, 19, 0, 0);
      $fridayClosing = Carbon::create(2017, 2, 20, 19, 0, 0);

      Category::create([
        'id' => 'animation',
        'name' => 'Animation',
        'compact_name' => 'Animation',
        'description' => 'A single animation programme (or a shortened edit from an episode or series), or an original piece of animation of any type, which has been produced by your station.',
        'closing_at' => $fridayClosing
      ]);

    }
}
