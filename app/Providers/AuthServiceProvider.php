<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		// App\Database\User::class => App\Policies\UserPolicy::class,
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();

		//

		Gate::define('station', function ($user) {
			return $user->type == "station";
		});
		Gate::define('support', function ($user) {
			return $user->type == "support" || $user->type == "admin";
		});
		Gate::define('admin', function ($user) {
			return $user->type == "admin";
		});
		Gate::define('judge', function ($user) {
			return $user->type == "judge";
		});
	}
}
