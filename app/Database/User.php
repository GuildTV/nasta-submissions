<?php

namespace App\Database;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Mail\Auth\ResetPassword;

use App\Database\Upload\StationFolder;

use Mail;

class User extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	protected $dates = [ 'last_login_at' ];

	public function uploadedFiles(){
		return $this->hasMany('App\Database\Upload\UploadedFile', 'station_id');
	}

	public function entries(){
		return $this->hasMany('App\Database\Entry\Entry', 'station_id')
      ->with('category', 'uploadedFiles', 'uploadedFiles.metadata');
	}

	public function stationFolder(){
		return $this->hasOne('App\Database\Upload\StationFolder');
	}

	public function stationFolders(){
		return $this->hasMany('App\Database\Upload\StationFolder');
	}

	public function sendPasswordResetNotification($token){
		Mail::to($this)->queue(new ResetPassword($this, $token));
	}

}
