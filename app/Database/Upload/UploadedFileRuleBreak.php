<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;


class UploadedFileRuleBreak extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uploaded_file_id', 
        'result', 'metadata',
        'warnings', 'errors',
    ];

    public function file()
    {
        return $this->belongsTo('App\Database\Upload\UploadedFile');
    }

}