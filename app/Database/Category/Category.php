<?php

namespace App\Database\Category;

use Illuminate\Database\Eloquent\Model;

use App\Database\Traits\HasPivotTrait;

use Carbon\Carbon;

use Config;
use Auth;

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
        'id', 'name', 'compact_name',
        'description',
        'closing_at',
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

    public function myEntry()
    {
        $user = Auth::user();
        if ($user == null)
            return null;

        return $this->hasOne('App\Database\Entry\Entry')->where('station_id', $user->id);
    }

    public function myEntryOrNew()
    {
        $entry = $this->myEntry;
        if ($entry != null)
            return $entry;

        return new self([
            'station_id' => $sid,
        ]);
    }

    public function getEntryForStation($sid){
        return $this->entries()
            ->firstOrNew([
                'station_id' => $sid,
            ]);
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
