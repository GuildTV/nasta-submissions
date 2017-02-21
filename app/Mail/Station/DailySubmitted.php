<?php

namespace App\Mail\Station;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Exceptions\InvalidArgumentException;

use App\Database\User;
use App\Database\Entry\Entry;
use App\Database\Category\Category;

use Carbon\Carbon;

class DailySubmitted extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $date;
    
    public $entries;
    public $categories;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Carbon $date)
    {
        $this->user = $user;
        $this->date = $date;
        
        $this->loadCategories();

        if (count($this->categories) == 0) 
            throw new InvalidArgumentException("no categories for date");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your submissions for ' . $this->date->format('l'))
            ->view('emails.station.daily-submitted')
            ->text('emails.station.daily-submitted_plain')
            ->with([
                'categories' => $this->categories,
                'entries' => $this->entries,
                'user' => $this->user,
            ]);
    }

    private function loadCategories(){
        $this->categories = Category::whereDate('closing_at', '=', $this->date->startOfDay()->toDateString())->get();
        $ids = [];
        foreach ($this->categories as $cat){
            $ids[] = $cat->id;
        }

        $entries = Entry::where("station_id", $this->user->id)
            ->whereIn('category_id', $ids)
            ->where('submitted', true)
            ->get();

        $this->entries = [];
        foreach ($entries as $entry) {
            $this->entries[$entry->category_id] = $entry;
        }
    }
}
