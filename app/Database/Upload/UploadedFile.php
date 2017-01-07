<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class UploadedFile extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'station_id', 'category_id',
        'account_id', 'path', 'name',
        'uploaded_at',
    ];

    protected $dates = ['uploaded_at'];


    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public function account()
    {
        return $this->belongsTo('App\Database\Upload\DropboxAccount');
    }

    public function station()
    {
        return $this->belongsTo('App\Database\User', 'station_id');
    }

    public function isLate($category=null){
        if ($category == null)
            $category = $this->category;

        if ($category == null)
            return false;

        return $this->uploaded_at->gt($category->closing_at);
    }

}
