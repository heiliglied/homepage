<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Extensions\DatabaseSessionHandler;
use Session;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
		/*
		Session::extend('custom', function ($app) {
			$table   = config('session.table');
			$minutes = config('session.lifetime');

			return new DatabaseSessionHandler($this->getDatabaseConnection(), $table, $minutes, $app);
		});
		*/
    }
	
	protected function getDatabaseConnection()
	{
		/*
		$connection = config('session.connection');

		return DB::connection($connection);
		*/
	}
}
