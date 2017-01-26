<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

use App\Database\User;

use App\Mail\Auth\Welcome;

use Carbon\Carbon;

use Log;
use Exception;
use Mail;
use DB;

class WelcomeEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:welcome {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a welcome email to each user of specified type';

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
        $type = $this->argument('type');
        $includeWithPasswords = $this->confirm('Send to those already with passwords?');

        $query = User::where('type', $type);
        if (!$includeWithPasswords)
            $query = $query->where('password', '');

        $users = $query->get();
        Log::info("Sending welcome emails to " . $users->count() . " users of type '" . $type . "'");

        $repo = Password::broker()->getRepository();

        foreach ($users as $user){
            $token = $repo->createNewToken();

            DB::table('password_resets')->where('email', $user->email)->delete();
            DB::table('password_resets')->insert([
                'email' => $user->email, 
                'token' => $token,
                'created_at' => Carbon::now()->addYear(),
            ]);

            Mail::to($user)->queue(new Welcome($user, $token));

            Log::info('Sent welcome email to ' . $user->name);
        }

        Log::info('Completed emails');
    }

}
