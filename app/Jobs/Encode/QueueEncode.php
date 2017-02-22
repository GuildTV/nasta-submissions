<?php
namespace App\Jobs\Encode;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;

use App\Database\Upload\UploadedFile;
use App\Database\Encode\EncodeJob;
use App\Database\Encode\EncodeWatch;

use Config;
use Exception;
use Log;

class QueueEncode implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $profile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file, $profile)
    {
        $this->file = $file;
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->file->path_local == null)
          return false;

        $srcFile = pathinfo($this->file->path_local);
        $destFile = @$srcFile['dirname'].'/'.$srcFile['filename'].'-fixed'.'.'.@$srcFile['extension'];

        $job = EncodeJob::create([
          'source_file' => $this->file->path_local,
          'destination_file' => $destFile,
          'format_id' => $this->profile,
          'status' => 'Not Encoding',
          'progress' => 0,
        ]);

        EncodeWatch::create([
          'uploaded_file_id' => $this->file->id,
          'job_id' => $job->id
        ]);

        return $job;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        ExceptionEmail::notifyAdmin($exception, "Queue encode file failure: File #" . $this->file->id);
    }
}
