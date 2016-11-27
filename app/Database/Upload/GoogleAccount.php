<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;

class GoogleAccount extends Model
{
    
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
    "id"
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
  ];

  public static function ChooseForNewUpload(){
    return self::where('enabled', true)->first(); // TODO
  }

}
