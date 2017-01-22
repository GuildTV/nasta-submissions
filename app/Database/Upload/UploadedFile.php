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

    public function metadata()
    {
        return $this->belongsTo('App\Database\Upload\VideoMetadata', "video_metadata_id");
    }

    public function isLate($category=null){
        if ($category == null && $this->category_id != null)
            $category = $this->category;

        if ($category == null)
            return false;

        return $this->uploaded_at->gt($category->closing_at);
    }

    public function getUrl($forceReload=false){
        if (!$forceReload && $this->public_url != null)
            return $this->public_url;

        $client = new DropboxFileServiceHelper($this->account->access_token);
        $this->public_url = $client->getPublicUrl($this->path);
        $this->public_url .= (parse_url($this->public_url, PHP_URL_QUERY) ? '&' : '?') . 'raw=1';

        if ($this->public_url != null){
            self::where('id', $this->id)->update([ 'public_url' => $this->public_url ]);
        }

        return $this->public_url;
    }

}
