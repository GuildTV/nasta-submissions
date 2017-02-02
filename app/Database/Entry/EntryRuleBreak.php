<?php

namespace App\Database\Entry;

use Illuminate\Database\Eloquent\Model;


class EntryRuleBreak extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id', 
        'result', 'constraint_map',
        'warnings', 'errors',
    ];

    public function entry()
    {
        return $this->belongsTo('App\Database\Entry\Entry');
    }

}