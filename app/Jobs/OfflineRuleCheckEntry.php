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
        if ($this->entry->submitted && !$this->overwrite) {
            Log::warning('Skipping rule check of entry #' . $this->entry->id . ', as it is not submitted');
            return false;
        }

        $files = $this->entry->uploadedFiles()->with('rule_break')->get()->sortByDesc(function($file, $k){
            if ($file->rule_break == null)
                return -1;

            return $file->rule_break->length;
        });
        $constraints = $this->entry->category->constraints()->orderBy('video_duration', 'desc')->get();

        $failures = [];
        $warnings = [];
        if ($files->count() != $constraints->count())
            $failures[] = "file_count";

        // compute score for files
        $scoredFiles = $this->calculateFileScoreSet($constraints, $files);

        $combinationsMatrix = $this->computeFileScoreMatrix($scoredFiles);
        $possibleCombinations = $this->filterPossibleCombinations($combinationsMatrix);

        $topCombination = $this->chooseBestCombination($possibleCombinations);      
        if ($constraints->count() != count($topCombination))
            $failures[] = "matched_file_count";

        $constraint_map = [];

        if ($topCombination != null){
            foreach ($topCombination as $fileInfo){
                $constraint_map[$fileInfo['file']->id] = $fileInfo['constraint']->id;

                // check length 
                if ($fileInfo['file']->rule_break == null){
                    $failures[] = 'file_result.missing=' . $fileInfo['file']->id;
                    continue;
                }

                if ($fileInfo['file']->rule_break->length > $fileInfo['constraint']->video_duration)
                    $failures[] = "file_too_long=" . $fileInfo['file']->id;

                switch($fileInfo['file']->rule_break->result) {
                    case 'unknown':
                        $failures[] = 'file_result.unknown=' . $fileInfo['file']->id;
                        break;
                    case 'warning':
                        $warnings[] = 'file_result.warning=' . $fileInfo['file']->id;
                        break;
                    case 'break':
                        $failures[] = 'file_result.break=' . $fileInfo['file']->id;
                        break;
                    case 'rejected':
                        $failures[] = 'file_result.rejected=' . $fileInfo['file']->id;
                        break;
                }
            }
        }

        if (!$this->entry->submitted)
            $warnings[] = 'not_submitted';

        $result = "ok";

        if (count($warnings) > 0){
            Log::warning("Entry has warnings: " . implode(", ", $warnings));
            $result = "warning";
        }

        if (count($failures) > 0){
            Log::error("Entry failed conform checks with issues: " . implode(", ", $failures));
            $result = "break";
        } else {
            Log::info("Entry passed conform checks");
        }

        $this->save([
            'entry_id' => $this->entry->id,
            'result' => $result,
            'constraint_map' => json_encode($constraint_map),
            'warnings' => json_encode($warnings), 
            'errors' => json_encode($failures),
        ]);
    }

    private function save($data){
        if ($this->entry->rule_break != null)
            $this->entry->rule_break->delete();

        return EntryRuleBreak::create($data);
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
                $score += abs($file['score']);
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