<?php

namespace App\Database\Category;

use Illuminate\Database\Eloquent\Model;

use App\Database\Traits\HasPivotTrait;

use Carbon\Carbon;

use Config;

class Category extends Model
{
    use HasPivotTrait;

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

    public function hasEntryForStation($sid){
        return $this->entries()
            ->where('station_id', $sid)
            ->count() > 0;
    }

    public function hasConstraint($id){ // TODO - make this nicer!
        foreach ($this->constraints as $constraint){
            if ($constraint->id == $id)
                return true;
        }
        return false;
    }

    public function canEditSubmissions(){
        return Carbon::now()->lt($this->closing_at->addMinutes(Config::get('nasta.late_edit_period')));
    }

    public function isCloseToDeadline(){
        return Carbon::now()->gt($this->closing_at->subMinutes(Config::get('nasta.close_to_deadline_threshold')))
            && Carbon::now()->lt($this->closing_at);
    }
}
