<?php

namespace Mitul\APIGenerator;

use Illuminate\Support\ServiceProvider;
use Mitul\APIGenerator\Commands\APIGeneratorCommand;

class APIGeneratorServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('mitul.generate.api', function ($app)
		{
			return new APIGeneratorCommand();
		});

		$this->commands('mitul.generate.api');
	}

}