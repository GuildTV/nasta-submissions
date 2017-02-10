<?php

namespace App\Database\Category;

use Illuminate\Database\Eloquent\Model;


class CategoryResult extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 
        'winner_id', 'winner_comment',
        'commended_id', 'commended_comment',
    ];

    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public function winner()
    {
        return $this->belongsTo('App\Database\Entry\Entry', 'winner_id');
    }
    public function commended()
    {
        return $this->belongsTo('App\Database\Entry\Entry', 'commended_id');
    }

}