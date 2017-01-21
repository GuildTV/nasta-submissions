<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Files\DropboxFileServiceHelper;

use Carbon\Carbon;

class UploadedFile extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'station_id', 'category_id',
        'account_id', 'path', 'name', 
        'size', 'hash', 'public_url',
        'uploaded_at', 
    ];

    protected $dates = ['uploaded_at'];


    public function category()
    {
        return $this->belongsTo('App\Database\Category\Category');
    }

    public function account()
    {
        return $this->belongsTo('App\Database\Upload\DropboxAccount');
    }

    public function station()
    {
        return $this->belongsTo('App\Database\User', 'station_id');
    }

    public function isLate($category=null){
        if ($category == null && $this->category_id != null)
            $category = $this->category;

        if ($category == null)
            return false;

        return $this->uploaded_at->gt($category->closing_at);
    }

    public function getUrl($forceReload=false){
        if (!$forceReload && $this->url != null)
            return $this->url;

        $client = new DropboxFileServiceHelper($this->account->access_token);
        $this->url = $client->getPublicUrl($this->path);

        if ($this->url != null){
            self::where('id', $this->id)->update([ 'url' => $url ]);
        }

        return $this->url;
    }

}
