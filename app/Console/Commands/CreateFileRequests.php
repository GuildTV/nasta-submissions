<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\GoogleHelper;
use App\Database\Upload\GoogleAccount;
use App\Database\Upload\DropboxAccount;
use App\Database\Category\Category;
use App\Database\User;
use App\Database\Upload\StationFolder;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use App\Mail\Admin\ExceptionEmail;

use Log;
use Exception;

class CreateFileRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-file-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempt to create missing file requests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cookieCache = []; // cache of cookies to use for the api

        $categories = Category::all();

        $accounts = DropboxAccount::all();
        $accountMap = [];
        foreach ($accounts as $acc){
            $accountMap[$acc->id] = $acc;
        }

        $stations = User::where('type', 'station')->orderBy('dropbox_account_id')->get();
        foreach ($stations as $station){
            Log::info("Creating file drops for " . $station->name);

            if ($station->dropbox_account_id == null){
                Log::warning("Missing default dropbox account id for station");
                continue;
            }

            $existing = $station->stationFolders;
            $existingCategories = [];
            foreach ($existing as $fold){
                if ($fold->category_id != null)
                    $existingCategories[] = $fold->category_id;
            }

            foreach ($categories as $category){
                if (in_array($category->id, $existingCategories)){
                    Log::info("Skipping " . $category->id);
                    continue;
                }

                if (!isset($cookieCache[$station->dropbox_account_id])) {
                    $cookie = $this->ask('Cookie string for ' . $station->dropbox_account_id .'?');
                    $token = $this->ask("'t' token value?");
                    $cookieCache[$station->dropbox_account_id] = [
                        "cookie" => $cookie,
                        "token" => $token,
                    ];   
                }

                try {
                    $title = $station->name . " - " . $category->name;
                    $folder = "/Drops/" . $station->compact_name . "_" . $category->compact_name;
                    $res = $this->createFolder(636854300, $title, $folder, $cookieCache[$station->dropbox_account_id]);

                    $json = json_decode($res, true);
                    $url = "https://www.dropbox.com/request/" . $json['file_collector']['token'];

                    StationFolder::create([
                        'user_id' => $station->id,
                        'account_id' => $station->dropbox_account_id,
                        'category_id' => $category->id,
                        'request_url' => $url,
                        'folder_name' => $folder
                    ]);

                    Log::info("Created " . $category->id);
                    // usleep(100 * 1000); // sleep 100ms

                } catch (Exception $e){
                    Log::error("Failed to create " . $category->id . " (" . $e->getMessage() . ")");
                    unset($cookieCache[$station->dropbox_account_id]);
                }
            }

        }
    }

    private function createFolder($dropbox_id, $title, $path, $authData){
        $body_data = http_build_query([
            "_subject_uid" => $dropbox_id, // TODO - define this!!
            "title" => $title,
            "path" => $path,
            "allow_uploads_by_email" => false,
            "hard_deadline_ts" => -1,
            "soft_deadline_ts" => -1,
            "is_xhr" => true,
            "t" => $authData['token']
        ]);

        $ch = curl_init("https://www.dropbox.com/drops/create");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "cookie: " . $authData['cookie'],
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
        ]);

        // write curl response to file
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body_data); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // get curl response
        $res = curl_exec($ch); 
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($status != 200)
            throw new Exception("Bad status code creating request");

        // close up
        curl_close($ch);

        return $res;
    }
}
