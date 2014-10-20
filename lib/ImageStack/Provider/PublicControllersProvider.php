<?php
namespace ImageStack\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerProviderInterface;
use ImageStack\Controller\Front;
use Symfony\Component\HttpFoundation\Request;

class PublicControllersProvider implements ServiceProviderInterface, ControllerProviderInterface
{
	function boot(Application $app) {
	}

	function register(Application $app) {
		$app['controller.front'] = $app->share(function() use ($app) {
			return new Front();
		});
	}

  public function connect(Application $app) {
	  $controllers = $app['controllers_factory'];
		
		$controllers->get('/{mount}/{path}', 'controller.front:image')
			->assert('mount', '[a-z0-9]+')
			->convert('mount', 'converter.mount:convert')
		  ->assert('path', '.+');

		return $controllers;
	}
}