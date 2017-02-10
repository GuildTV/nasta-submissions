<?php

namespace App\Mail\Judge;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\Category\Category;

use Carbon\Carbon;

class CategoryJudged extends Mailable
{
    use Queueable, SerializesModels;

    private $category;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Category $category, Carbon $date=null)
    {
        $this->category = $category;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Category ' . $this->category->name . ' judged')
            ->view('emails.judge.category-judged')
            ->text('emails.judge.category-judged_plain')
            ->with([
                'category' => $this->category,
            ]);
    }
}
