<?php

namespace App\Database\Entry;

use App\Helpers\TextHelper;

use App\Exceptions\Database\ValueException;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FileUpload extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'station_id', 'category_id', 'constraint_id',
        'account_id', 'scratch_folder_id',
    ];


    public function constraint()
    {
        return $this->belongsTo('App\Database\Category\FileConstraint');
    }

    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public function entry()
    {
        return $this->belongsTo('App\Database\Entry\Entry');
    }

    public function account()
    {
        return $this->belongsTo('App\Database\Upload\GoogleAccount');
    }

    public function station()
    {
        return $this->belongsTo('App\Database\User', 'station_id');
    }

}
