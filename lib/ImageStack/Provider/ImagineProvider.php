<?php
namespace ImageStack\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ImagineProvider implements ServiceProviderInterface {

	function boot(Application $app) {
	}
	
	function register(Application $app) {
		$app['imagine'] = $app->share(function() use ($app) {
			
			$driver = 'gd';
			if (isset($app['config']['imagine.driver'])) {
				$driver = $app['config']['imagine.driver'];
			}
			switch ($driver) {
				case 'imagick':
					return new \Imagine\Imagick\Imagine();
				case 'gmagick':
					return new \Imagine\Gmagick\Imagine();
				default:
					return new \Imagine\Gd\Imagine();
			}
		});
	}
}