<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Database\Category\FileConstraint;

class FileConstraintsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // 1
      FileConstraint::create([
        'name' => '10 Minute Video',
        'description' => 'A 10 minute video entry',
        'mimetypes' => 'video/mp4'
      ]);

      // 2
      FileConstraint::create([
        'name' => '500 Words',
        'description' => 'A 500 word document',
        'mimetypes' => 'application/pdf'
      ]);
    }
}
