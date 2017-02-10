<?php

use Illuminate\Database\Seeder;

use App\Database\User;
use App\Database\Category\Category;
use App\Database\Entry\Entry;
use App\Database\Entry\EntryRuleBreak;
use App\Database\Upload\DropboxAccount;
use App\Database\Upload\UploadedFile;
use App\Database\Upload\VideoMetadata;

class FakeEntriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $db_account = DropboxAccount::first();

        $categories = Category::all();
        foreach ($categories as $cat){
            // choose 10 stations to create entries for
            $stations = User::where('type', 'station')->inRandomOrder()->take(10)->get();

            foreach ($stations as $station) {
                if (Entry::where('station_id', $station->id)->where('category_id', $cat->id)->count() > 0)
                    continue;

                $entry = Entry::create([
                    'station_id' => $station->id,
                    'category_id' => $cat->id,
                    'name' => $faker->realText(255),
                    'description' => $faker->realText(2000),
                    'rules' => $faker->boolean(),
                    'submitted' => $faker->boolean(),
                ]);

                // create files
                $constraint_map = [];
                foreach ($cat->constraints as $constraint) {
                    if (!$faker->boolean(80))
                        continue;

                    //create video metadata
                    $metadata_id = null;
                    if ($faker->boolean(80)) {
                        $metadata = VideoMetadata::create([
                            'width' => $faker->randomNumber(4),
                            'height' => $faker->randomNumber(4),
                            'duration' => $faker->randomNumber(5),
                        ]);
                        $metadata_id = $metadata->id;
                    }

                    $file = UploadedFile::create([
                        'station_id' => $station->id,
                        'category_id' => $cat->id,
                        'account_id' => $db_account->id,
                        'path' => str_random(10),
                        'name' => $faker->realText(255),
                        'size' => $faker->randomNumber(5),
                        'hash' => str_random(40),
                        'path_local' => str_random(20),
                        'public_url' => 'http://localhost/' . str_random(20),
                        'video_metadata_id' => $metadata_id,
                    ]);

                    $constraint_map[$file->id] = $constraint->id;
                }

                // create rule break entry
                if ($faker->boolean(90)) {
                    $break = EntryRuleBreak::create([
                        'entry_id' => $entry->id,
                        'result' => $faker->randomElement([ 'break', 'unknown', 'warning', 'ok' ]),
                        'constraint_map' => json_encode($constraint_map),
                        'warnings' => '[]',
                        'errors' => '[]',
                    ]);
                }

            }
        }
        
    }
}
