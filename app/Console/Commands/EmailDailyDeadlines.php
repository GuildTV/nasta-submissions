<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Database\Category\Category;
use App\Database\User;

use App\Mail\Station\DailyDeadlines;

use Carbon\Carbon;

use Log;
use Exception;
use Mail;

class EmailDailyDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a daily deadlines email to stations';

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
      $deadlines = Category::whereDate('closing_at', '=', Carbon::now()->startOfDay()->toDateString())->count();
      if ($deadlines == 0)
        return "NO_DEADLINES";

      $users = User::where("type", "station")->get();
      if (count($users) == 0)
        return "NO_USERS";

      foreach ($users as $user) {
        $helper = new DailyDeadlines($user, Carbon::now());
        Mail::to($user)->send($helper);
      }

      Log::info('Sent emails to ' . count($users) . ' stations');
      return count($users);
    }
}
