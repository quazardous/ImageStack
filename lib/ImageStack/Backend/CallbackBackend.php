<?php
namespace ImageStack\Backend;

use ImageStack\Image;
use ImageStack\OptionableComponent;
use ImageStack\Backend\BackendInterface;

/**
 * Backend allowing to use a callback arround another backend.
 *
 */
class CallbackBackend extends OptionableComponent implements BackendInterface {
	
  /**
   * Backend source
   * @var \ImageStack\Backend\BackendInterface
   */
  protected $backend;
  
  protected $callback;
  
  /**
   * 
   * @param BackendInterface $backend
   * @param callable $callback
   * @param array $options
   * @throws \InvalidArgumentException
   * 
   * The callbavle should be something like :
   * <code>
   * <?php
   * function(BackendInterface $backend, $path) {
   *   return $backend->getImage($path);
   * }
   * ?>
   * </code>
   */
	public function __construct(BackendInterface $backend, $callback, $options = array()) {
    $this->backend = $backend;
    $this->callback = $callback;
		parent::__construct($options);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ImageStack\Backend\BackendInterface::getImage()
	 */
	public function getImage($path) {
		return call_user_func($this->callback, $this->backend, $path);
	}
}