<?php

namespace App\Database\Category;

use Illuminate\Database\Eloquent\Model;

use App\Database\Traits\HasPivotTrait;

use App\Database\Entry\Entry;

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

        $user = Auth::user();
        if ($user == null)
            return null;

        return new Entry([
            'station_id' => $user->id,
        ]);
    }

    public function getEntryForStation($sid){
        return $this->entries()
            ->firstOrNew([
                'station_id' => $sid,
            ]);
    }

    public function hasEntryForStation($sid){
        return $this->entries()->where('station_id', $sid)->count() > 0;
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

    public static function getAllGrouped($date, $loadMyEntry=false){
        $query = self::query();

        if ($loadMyEntry)
            $query = $query->with('myEntry')->with('myEntry.uploadedFiles');

        if ($date != null)
            $query = $query->whereDate('closing_at', '=', $date->startOfDay()->toDateString());

        $res = $query->orderBy("closing_at")->orderBy("name")->get();

        $categories = [];

        foreach ($res as $cat) {
            $roundedDate = $cat->closing_at->startOfDay()->toIso8601String();

            if (!isset($categories[$roundedDate]))
                $categories[$roundedDate] = [];

            $categories[$roundedDate][] = $cat;
        }

        return $categories;
    }
}
