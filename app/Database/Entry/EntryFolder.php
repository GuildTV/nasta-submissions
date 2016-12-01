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
        'station_id', 'category_id',
        'folder_id',
    ];

    public function entry()
    {
        return $this->category->getEntryForStation($this->station_id);
    }

    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public static function findForStation($sid, $cid){
        return self::where('station_id', $sid)
            ->where('category_id', $cid)
            ->first();
    }
}
