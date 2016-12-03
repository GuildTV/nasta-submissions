<?php

namespace App\Database\Entry;

use Illuminate\Database\Eloquent\Model;

use App\Database\Entry\EntryFolder;

class Entry extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

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


    public function folder()
    {
        return $this->hasOne('App\Database\Entry\EntryFolder');
    }

    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public function findForStation($sid, $cid){
        return self::where('station_id', $sid)
            ->where('category_id', $cid)
            ->first();
    }

    public function uploadedFiles(){
        return $this->hasMany('App\Database\Upload\UploadedFile', 'category_id', 'category_id')
            ->where('station_id', $this->station_id);
    }
}
