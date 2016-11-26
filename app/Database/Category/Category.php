<?php

namespace App\Database\Category;

use App\Helpers\TextHelper;

use App\Exceptions\Database\ValueException;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';
    
    /**
     * Set id as not incrementing, for string type.
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
    ];


    protected $dates = ['closing_at', 'opening_at'];


    public function constraints()
    {
        return $this->belongsToMany('App\Database\Category\FileConstraint')->withTimestamps();
    }

    public function entries()
    {
        return $this->hasMany('App\Database\Entry\Entry');
    }


    public function getEntryForStation($sid){
        return $this->entries()
            ->firstOrNew([
                'station_id' => $sid,
            ]);
    }
}
