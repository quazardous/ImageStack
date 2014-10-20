<?php
namespace ImageStack\Backend;

use ImageStack\Image;
use ImageStack\OptionableComponent;
use ImageStack\Service\AppableServiceInterface;

/**
 * Backend pile.
 * Appelle successivement plusieurs backends. S'arre au premier qui renvoie une image.
 *
 */
class StackBackend extends OptionableComponent implements BackendInterface {
	
  /**
   * @var BackendInterface[]
   */
  protected $backends;
  
	/**
	 * @param BackendInterface[] $backends
	 * @param array $options
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $backends, $options = array()) {
	  
	  $this->backends = $backends;
	  		
		parent::__construct($options);

	}

	/**
	 * (non-PHPdoc)
	 * @see \ImageStack\Backend\BackendInterface::getImage()
	 */
	public function getImage($path) {
		foreach ($this->backends as $backend) {
		  if ($image = $backend->getImage($path)) {
		    $this->app['logger']->info(sprintf("%s > %s [%s] =OK", $this->getName(), $backend->getName(), $path));
		    return $image;
		  }
		  $this->app['logger']->info(sprintf("%s > %s [%s] =KO", $this->getName(), $backend->getName(), $path));
		}
		return false;
	}
	
}