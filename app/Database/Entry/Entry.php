<?php

namespace App\Database\Entry;

use Illuminate\Database\Eloquent\Model;

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
        'station_id', 'category_id',
        'name', 'description',
    ];

    
    public function station()
    {
        return $this->belongsTo('App\Database\User', 'station_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public function rule_break()
    {
        return $this->hasOne('App\Database\Entry\EntryRuleBreak');
    }

    public function findForStation($sid, $cid){
        return self::where('station_id', $sid)
            ->where('category_id', $cid)
            ->first();
    }

    public function uploadedFiles(){ // Note: cannot be eager/lazy loaded
        return $this->hasMany('App\Database\Upload\UploadedFile', 'category_id', 'category_id')
            ->with('metadata')
            ->whereNull('replacement_id')
            ->where('station_id', $this->station_id);
    }

    public function allUploadedFiles(){ // Note: cannot be eager/lazy loaded
        return $this->hasMany('App\Database\Upload\UploadedFile', 'category_id', 'category_id')
            ->with('metadata')
            ->where('station_id', $this->station_id);
    }

    public function uploadedFileLog(){ // Note: cannot be eager/lazy loaded
        return $this->hasMany('App\Database\Upload\UploadedFileLog', 'category_id', 'category_id')
            ->where('station_id', $this->station_id);
    }

    public function countReasonsLate($category=null){
        if ($category == null)
            $category = $this->category;

        $reasons = 0;

        if ($this->updated_at != null && $this->updated_at->gt($category->closing_at))
            $reasons++;

        foreach ($this->uploadedFiles as $file){
            if ($file->hasReplacement())
                continue;
            
            if ($file->isLate($category))
                $reasons++;
        }

        return $reasons;
    }

    public function isLate($category=null){
        return $this->countReasonsLate($category) > 0;
    }
}
