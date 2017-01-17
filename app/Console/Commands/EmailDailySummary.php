<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Database\Category\Category;
use App\Database\User;

use App\Mail\Station\DailySubmitted;

use Carbon\Carbon;

use Log;
use Exception;
use Mail;

class EmailDailySummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a daily summary email to stations';

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

      $sent = 0;

      foreach ($users as $user) {
        $helper = new DailySubmitted($user, Carbon::now());

        // Skip station that has no categories
        if (count($helper->entries) == 0)
            continue;

        Mail::to($user)->queue($helper);
        $sent++;
      }

      Log::info('Sent emails to ' . $sent . ' stations');
      return $sent;
    }
}
