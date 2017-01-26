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

      // Best Broadcaster
      $this->add('best-broadcaster', 1);
      $this->add('best-broadcaster', 7);

      // Cinematography
      $this->add('cinematography', 1);

      // Comedy
      $this->add('comedy', 1);

      // Documentary
      $this->add('documentary', 1);

      // Drama
      $this->add('drama', 1);

      // Factual
      $this->add('factual', 1);

      // Freshers Coverage
      $this->add('freshers', 1);

      // Ident
      $this->add('ident', 4);

      // Light Entertainment
      $this->add('light-entertainment', 1);

      // Live Broadcast
      $this->add('live', 1);

      // Mars el Brogy
      $this->add('mars-el-brogy', 1);
      $this->add('mars-el-brogy', 7);
      $this->add('mars-el-brogy', 9);

      // Music Programming
      $this->add('music', 1);

      // News and Current Affairs
      $this->add('news-current-affairs', 1);

      // On-Screen Female
      $this->add('on-screen-female', 2);

      // On-Screen Male
      $this->add('on-screen-male', 2);

      // Open
      $this->add('open', 1);

      // Post Production
      $this->add('post-production', 1);

      // Sport
      $this->add('sport', 1);

      // Station Marketing
      $this->add('marketing', 1);
      $this->add('marketing', 7);

      // Technical Achievement
      $this->add('technical', 7);

      // Tim Marshall award for Special Recognition
      $this->add('tim-marshall', 8);
      $this->add('tim-marshall', 9);

      // Title Sequence
      $this->add('title', 3);

      // Writing
      $this->add('writing', 6);
      $this->add('writing', 10);

    }

    private function add($category_id, $constraint_id){
      DB::table('category_file_constraint')->insert(
        [ "category_id" => $category_id, "file_constraint_id" => $constraint_id ]
      );
    }
}
