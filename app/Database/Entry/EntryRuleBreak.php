<?php

namespace App\Database\Entry;

use Illuminate\Database\Eloquent\Model;


class EntryRuleBreak extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id', 
        'result', 'notes', 'constraint_map',
        'warnings', 'errors',
    ];

    public function entry()
    {
        return $this->belongsTo('App\Database\Entry\Entry');
    }

}