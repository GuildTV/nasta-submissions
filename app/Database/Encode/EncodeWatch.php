<?php 
namespace App\Database\Encode;

use Illuminate\Database\Eloquent\Model;

class EncodeWatch extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'encode_watch';

  protected $fillable = array('uploaded_file_id', 'job_id');

  public function job(){
    return $this->hasOne('App\Database\Encode\EncodeJob', 'id', 'job_id');
  }

  public function file(){
    return $this->belongsTo('App\Database\Upload\UploadedFile', 'uploaded_file_id');
  }

}