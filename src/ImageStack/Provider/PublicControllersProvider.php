<?php
namespace ImageStack\Provider;

use Silex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\ControllerProviderInterface;
use ImageStack\Controller\Front;
use Symfony\Component\HttpFoundation\Request;

class PublicControllersProvider implements ServiceProviderInterface, ControllerProviderInterface
{

	function register(Container $app) {
		$app['controller.front'] = function() use ($app) {
			return new Front();
		};
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