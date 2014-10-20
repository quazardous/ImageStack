<?php
namespace ImageStack;

use Silex\Application as ApplicationBase;
use ImageStack\Service\AppableServiceInterface;

class Application extends ApplicationBase {
	/**
	 * On surcharge cette méthode pour injecter automatiquement l'object $app.
	 */
	public static function share($callable)
	{
		if (!is_object($callable) || !method_exists($callable, '__invoke')) {
			throw new InvalidArgumentException('Service definition is not a Closure or invokable object.');
		}
	
		return function ($c) use ($callable) {
			static $object;
	
			if (null === $object) {
				$object = $callable($c);
				if ($object instanceof AppableServiceInterface) {
					// et hop on injecte $app !
					$object->setApp($c);
				}
			}
	
			return $object;
		};
	}
}