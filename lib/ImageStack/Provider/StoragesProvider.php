<?php
namespace ImageStack\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use ImageStack\Storage\OptimizedFileStorage;

class StoragesProvider implements ServiceProviderInterface {
	
	function boot(Application $app) {
	}
	
	function register(Application $app) {
		$app['storage.default'] = $app->share(function() use ($app) {
			return new OptimizedFileStorage($app['config']['storage.default']);
		});
	}
}