<?php

namespace App\Database\Entry;

use Illuminate\Database\Eloquent\Model;


class EntryResult extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id', 
        'score', 'feedback',
    ];

    public function entry()
    {
        return $this->belongsTo('App\Database\Entry\Entry');
    }

}