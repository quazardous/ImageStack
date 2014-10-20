<?php
namespace ImageStack\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use ImageStack\Converter\Mount;

class ConvertersProvider implements ServiceProviderInterface {
	
	function boot(Application $app) {
	}
	
	function register(Application $app) {
		$app['converter.mount'] = $app->share(function() {
			return new Mount();
		});
	}
}