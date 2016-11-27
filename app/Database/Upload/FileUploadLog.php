<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileUploadLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'station_id', 'category_id', 'constraint_id',
        'message', 'level',
    ];


    public function constraint()
    {
        return $this->belongsTo('App\Database\Category\FileConstraint');
    }

    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public function station()
    {
        return $this->belongsTo('App\Database\User', 'station_id');
    }

}
