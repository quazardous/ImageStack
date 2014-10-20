<?php
namespace ImageStack\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use ImageStack\Converter\Mount;

class ConvertersProvider implements ServiceProviderInterface {
	
	function register(Container $app) {
		$app['converter.mount'] = function() {
			return new Mount();
		};
	}
}