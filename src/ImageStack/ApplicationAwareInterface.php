<?php
namespace ImageStack;

/**
 * Interface for application aware components.
 *
 */
interface ApplicationAwareInterface {
	/**
	 * @param \ImageStack\Application $app
	 */
	public function setApp(Application $app);
	
	/**
	 * @return \ImageStack\Application $app
	 */
	public function getApp();
}