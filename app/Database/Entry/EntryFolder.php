<?php

namespace App\Database\Entry;

use Illuminate\Database\Eloquent\Model;

class EntryFolder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entries_folders';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id', 'folder_id',
    ];

    public function entry()
    {
        return $this->belongsTo('App\Database\Entry\Entry');
    }

}
