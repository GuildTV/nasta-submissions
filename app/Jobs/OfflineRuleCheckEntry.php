<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;

use App\Database\Entry\Entry;
use App\Database\Entry\EntryRuleBreak;

use Config;
use Exception;
use Log;

class OfflineRuleCheckEntry implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $entry;
    protected $overwrite;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Entry $entry, $overwrite=false)
    {
        $this->entry = $entry;
        $this->overwrite = $overwrite;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // If not submitted, nothing to do!
        if ($this->entry->submitted) {
            Log::warning('Skipping rule check of entry #' . $this->file->id . ', as it is not submitted');
            return false;
        }

        $files = $this->entry->uploadedFiles()->with('rule_break')->get()->sortByDesc(function($file, $k){
            if ($file->rule_break == null)
                return -1;

            return $file->rule_break->length;
        });
        $constraints = $this->entry->category->constraints()->orderBy('video_duration', 'desc')->get();

        $failures = [];
        if ($files->count() != $constraints->count())
            $failures[] = "file_count";

        // compute score for files
        $scoredFiles = $this->calculateFileScoreSet($constraints, $files);

        $combinationsMatrix = $this->computeFileScoreMatrix($scoredFiles);
        $possibleCombinations = $this->filterPossibleCombinations($combinationsMatrix);

        $topCombination = $this->chooseBestCombination($possibleCombinations);

        

        dd ($topCombination);

        // TODO - now work with this!!


    }

    private function calculateFileScoreSet($constraints, $files){
        $scoredFiles = [];
        foreach ($constraints as $constraint){
            $filesWithScores = [];
            foreach ($files as $file){

                $filesWithScores[] = [
                    "file" => $file,
                    "score" => $this->calculateFileScore($constraint, $file),
                ];
            }

            $scoredFiles[] = [
                "constraint" => $constraint,
                "files" => $filesWithScores
            ];
        }

        return $scoredFiles;
    }

    private function calculateFileScore($constraint, $file){
        if ($file->rule_break == null)
            return 99999;

        $mimes = explode(";", $constraint->mimetypes);
        if (!in_array($file->rule_break->mimetype, $mimes))
            return 9999;

        $lengthDiff = $file->rule_break->length - $constraint->video_duration;
        if ($lengthDiff < 0)
            return $lengthDiff / 2;

        return $lengthDiff;
    }

    private function computeFileScoreMatrix($scoredFiles){
        $options = null;

        foreach ($scoredFiles as $scoredConstraint){
            $options = $this->addToOptions($options, $scoredConstraint['files'], $scoredConstraint['constraint']);
        }

        return $options;
    }

    private function addToOptions($options, $files, $constraint){
        if ($options == null){
            $options = [];

            foreach ($files as $f){
                $options[] = [ array_merge([ 'constraint' => $constraint ], $f) ];
            }

            return $options;
        }

        $newOptions = [];
        foreach ($options as $opt){
            foreach ($files as $f){
                $newOptions[] = array_merge([ array_merge([ 'constraint' => $constraint ], $f) ], $opt);
            }
        }

        return $newOptions;
    }

    private function filterPossibleCombinations($combinationsMatrix){
        $options = [];

        foreach ($combinationsMatrix as $comb){
            $matchedIds = [];
            $fail = false;

            foreach ($comb as $file){
                $fid = $file['file']->id;
                if (in_array($fid, $matchedIds)){
                    $fail = true;
                    break;
                }

                $matchedIds[] = $fid;
            }

            if (!$fail)
                $options[] = $comb;
        }

        return $options;
    }

    private function chooseBestCombination($possibleCombinations){
        $minScore = 999999999999;
        $minObject = null;

        foreach ($possibleCombinations as $comb){
            $score = 0;
            foreach ($comb as $file){
                $score += $file['score'];
            }

            if ($score < $minScore){
                $minScore = $score;
                $minObject = $comb;
            }
            if ($score == $minScore){
                // TODO - handle in some way!
            }
        }

        return $minObject;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        ExceptionEmail::notifyAdmin($exception, "Failed entry rule check: Entry #" . $this->entry->id);
    }

}