<?php
namespace App\Jobs\Encode;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;

use App\Database\Encode\EncodeWatch;

use App\Jobs\Encode\UploadEncoded;

use Config;
use Exception;
use Log;

class ScrapeWatch implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $watches = EncodeWatch::with('job')->get();
        $finished = 0;

        foreach ($watches as $watch){
            if ($watch->job == null){
                $watch->delete();
                continue;
            }

            if (!$watch->job->isFinished())
                continue;

            try {
                dispatch((new UploadEncoded($watch->job->destination_file, $watch->file))->onQueue('uploads'));
                $watch->delete();
                $finished++;

            } catch (Exception $e){
                Log::error('Failed to run: '. $e->getMessage());
            }
        }

        return $finished;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        ExceptionEmail::notifyAdmin($exception, "Failed to scrape encode jobs being watched");
    }
}
