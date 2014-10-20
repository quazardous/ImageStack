<?php
namespace ImageStack\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use ImageStack\Storage\OptimizedFileStorage;

class StoragesProvider implements ServiceProviderInterface {
	
	function boot(Container $app) {
	}
	
	function register(Container $app) {
		$app['storage.default'] = function() use ($app) {
			return new OptimizedFileStorage($app['config']['storage.default']);
		};
	}
}