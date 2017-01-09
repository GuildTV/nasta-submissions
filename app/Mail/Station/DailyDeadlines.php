<?php

namespace App\Mail\Station;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Exceptions\InvalidArgumentException;

use App\Database\User;
use App\Database\Category\Category;

use Carbon\Carbon;

class DailyDeadlines extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $date;

    public $groupedCategories;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Carbon $date=null)
    {
        $this->user = $user;
        $this->date = $date;
        $this->groupedCategories = $this->getGroupedCategories($date);

        if (count($this->groupedCategories) == 0) 
            throw new InvalidArgumentException("no categories for date");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Submission deadlines' . ($this->date ? ' for ' . $this->date->format('l') : ''))
            ->view('emails.station.daily-deadlines')
            ->text('emails.station.daily-deadlines_plain')
            ->with([
                'groupedCategories' => $this->groupedCategories,
                'user' => $this->user,
            ]);
    }

    private function getGroupedCategories($date){
        $res = $date == null 
            ? Category::all()
            : Category::whereDate('closing_at', '=', $date->startOfDay()->toDateString())->get();

        $categories = [];

        foreach ($res as $cat) {
            $roundedDate = $cat->closing_at->startOfDay()->toIso8601String();

            if (!isset($categories[$roundedDate]))
                $categories[$roundedDate] = [];

            $categories[$roundedDate][] = $cat;
        }

        return $categories;
    }
}
