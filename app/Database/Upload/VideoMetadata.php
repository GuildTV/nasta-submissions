<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Files\DropboxFileServiceHelper;

use Carbon\Carbon;

class VideoMetadata extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'video_metadata';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'width', 'height',
        'duration', 
    ];

    public function file()
    {
        return $this->hasOne('App\Database\Upload\UploadedFile');
    }

}
