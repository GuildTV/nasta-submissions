<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Database\Category\Category;
use App\Database\User;

use Log;
use Exception;
use Hash;

class CreateJudge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-judge {id : The ID of the category}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a judge for the specified category';

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
        $id = $this->argument('id');
        $cat = Category::find($id);
        if ($cat == null){
            print "Category does not exist\n";
            return;
        }

        if ($cat->judge_id != null){
            print "Category already has a judge\n";
            return;
        }

        $name = $this->ask("Judge name:");

        $username = strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $name));
        print "The username is: " . $username . "\n";

        $password = str_random(10);
        print "The password is: " . $password . "\n";

        $user = new User();
        $user->name = $name;
        $user->compact_name = preg_replace('/\s+/', '', $name);
        $user->username = $username;
        $user->email = $username . '.nasta@guildtv.co.uk';
        $user->type = 'judge';
        $user->password = Hash::make($password);
        $user->save();

        $cat->judge_id = $user->id;
        $cat->save();
    }

}
