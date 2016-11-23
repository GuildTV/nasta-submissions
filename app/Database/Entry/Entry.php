<?php

namespace App\Database\Entry;

use App\Helpers\TextHelper;

use App\Exceptions\Database\ValueException;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Entry extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entries';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'station_id',
        'name', 'description',
    ];


    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public function findForStation($sid, $cid){
        return self::where('station_id', $sid)
            ->where('category_id', $cid)
            ->first();
    }
}
