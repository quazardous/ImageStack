<?php
namespace ImageStack;

use Silex\Application as ApplicationBase;

/**
 * Les services reçoivent automatiquement l'objet $app.
 *
 */
interface ApplicationAwareInterface {
	/**
	 * @param \ImageStack\Application $app
	 */
	public function setApp(ApplicationBase $app);
}