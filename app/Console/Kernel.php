<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ScrapeFreeSpace::class,
        Commands\ScrapeUploads::class,
        Commands\EmailDailyDeadlines::class,
        Commands\EmailDailySummary::class,
        Commands\CreateFileRequests::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('scrape:free-space')
                 ->everyFiveMinutes();
        $schedule->command('scrape:uploads')
                 ->everyMinute();
        $schedule->command('email:daily-deadlines')
                 ->dailyAt('09:00');
        $schedule->command('email:daily-summary')
                 ->dailyAt('19:10'); // 10 minutes past last deadline
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
