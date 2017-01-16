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
        'name' => '5 Minute Video',
        'description' => 'A 5 minute video entry',
        'mimetypes' => 'video/mp4'
      ]);

      // 3
      FileConstraint::create([
        'name' => '90 Second Video',
        'description' => 'A 90 second video entry',
        'mimetypes' => 'video/mp4'
      ]);

      // 4
      FileConstraint::create([
        'name' => '30 Second Video',
        'description' => 'A 30 second video entry',
        'mimetypes' => 'video/mp4'
      ]);

      // 5
      FileConstraint::create([
        'name' => 'Written report',
        'description' => 'A written report is required',
        'mimetypes' => 'application/pdf;text/plain'
      ]);

      // 6
      FileConstraint::create([
        'name' => '30 Page Script',
        'description' => 'A 30 page script',
        'mimetypes' => 'application/pdf;text/plain'
      ]);

      // 7
      FileConstraint::create([
        'name' => '500 Words',
        'description' => 'A 500 word document',
        'mimetypes' => 'application/pdf;text/plain'
      ]);

      // 8
      FileConstraint::create([
        'name' => '750 Words',
        'description' => 'A 750 word document',
        'mimetypes' => 'application/pdf;text/plain'
      ]);

      // 9
      FileConstraint::create([
        'name' => 'Online interview',
        'description' => 'There will be an online interview for selected entries.',
        'mimetypes' => ''
      ]);

      // 10
      FileConstraint::create([
        'name' => '5 Minute Video',
        'description' => 'A 5 minute video unjudged reference video',
        'mimetypes' => 'video/mp4'
      ]);
    }
}
