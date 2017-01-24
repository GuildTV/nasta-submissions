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


	public function stationFolder(){
		return $this->hasOne('App\Database\Upload\StationFolder');
	}

	public function stationFolderOrNew(){
		$folder = $this->stationFolder;
		if ($folder != null)
			return $folder;

		return new StationFolder([
			'user_id' => $this->id,
		]);
	}

	public function sendPasswordResetNotification($token){
		Mail::to($this)->queue(new ResetPassword($this, $token));
	}

}
