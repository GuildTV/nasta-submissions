<?php 
namespace App\Database\Encode;

use Illuminate\Database\Eloquent\Model;

use Config;
use Auth;
use File;
use Session;

class EncodeJob extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'encode_jobs';
  protected $connection = 'encode_mysql';
  public $timestamps = false;

  public function __construct(array $attributes = array()) 
  {
    parent::__construct($attributes);

    // allow this to vary for testing
    $this->connection = env('ENCODE_DB_CONNECTION', 'encode_mysql');
  }


  protected $fillable = array('source_file', 'destination_file', 'format_id', 'status', 'progress');

  public function isFinished(){
    return $this->status == "Done";
  }

  public function getProgress(){
    return ($this->status=="Encoding Pass 2"?50:0)+intval($this->progress/2);
  }
}