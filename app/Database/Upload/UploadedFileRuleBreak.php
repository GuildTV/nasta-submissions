<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;


class UploadedFileRuleBreak extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uploaded_file_id', 
        'result', 'notes', 'metadata',
        'mimetype', 'length',
        'warnings', 'errors',
    ];

    public function file()
    {
        return $this->belongsTo('App\Database\Upload\UploadedFile');
    }

}