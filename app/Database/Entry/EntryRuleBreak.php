<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;


class EntryRuleBreak extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uploaded_file_id', 
        'result',
        'warnings', 'errors',
    ];

    public function entry()
    {
        return $this->belongsTo('App\Database\Entry\Entry');
    }

}