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
        'description' => 'The submission must not exceed 10 minutes.',
        'mimetypes' => 'video/mp4'
      ]);

      // 2
      FileConstraint::create([
        'name' => '5 Minute Video',
        'description' => 'The submission must not exceed 5 minutes.',
        'mimetypes' => 'video/mp4'
      ]);

      // 3
      FileConstraint::create([
        'name' => '90 Second Video',
        'description' => 'The submission must not exceed 90 seconds.',
        'mimetypes' => 'video/mp4'
      ]);

      // 4
      FileConstraint::create([
        'name' => '30 Second Video',
        'description' => 'The submission must not exceed 30 seconds.',
        'mimetypes' => 'video/mp4'
      ]);

      // 5 - UNUSED
      FileConstraint::create([
        'name' => 'BLANK',
        'description' => '',
        'mimetypes' => ''
      ]);

      // 6
      FileConstraint::create([
        'name' => '30 Page Script',
        'description' => 'The category requires a script of 30 pages maximum. Writing may be from a single programme or shortened from an episode or series.',
        'mimetypes' => 'application/pdf;text/plain'
      ]);

      // 7
      FileConstraint::create([
        'name' => '500 Words',
        'description' => 'Written accompaniments will be accepted in .pdf or plain text format and must not exceed 500 words.',
        'mimetypes' => 'application/pdf;text/plain'
      ]);

      // 8
      FileConstraint::create([
        'name' => '750 Words',
        'description' => 'Written accompaniments will be accepted in .pdf or plain text format and must not exceed 750 words.',
        'mimetypes' => 'application/pdf;text/plain'
      ]);

      // 9
      FileConstraint::create([
        'name' => 'Online interview',
        'description' => 'There will be an online interview for selected stations.',
        'mimetypes' => ''
      ]);

      // 10
      FileConstraint::create([
        'name' => '5 Minute Video',
        'description' => 'A 5 minute video (maximum). This is for reference, rather than being judged for its own qualities. It should demonstrate how the submitted script has been visualised on screen.',
        'mimetypes' => 'video/mp4'
      ]);
    }
}
