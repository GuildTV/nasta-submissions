<?php

namespace App\Database\Upload;

use Illuminate\Database\Eloquent\Model;

class StationFolder extends Model
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    "user_id", 
    "account_id", "request_url", "folder_name",
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
  ];


}
