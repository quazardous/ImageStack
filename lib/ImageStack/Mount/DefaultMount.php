<?php
namespace ImageStack\Mount;

use ImageStack\Storage\StorageInterface;
use ImageStack\Backend\BackendInterface;
use ImageStack\OptionableComponent;

class DefaultMount extends OptionableComponent implements MountInterface {
	
	protected $mount;
	
	/**
	 * 
	 * @var BackendInterface
	 */
	protected $backend;
	
	/**
	 * 
	 * @var StorageInterface
	 */
	protected $storage;
	
	public function __construct($mount, BackendInterface $backend, StorageInterface $storage, $options = array()) {
		parent::__construct($options);
		$this->mount = rtrim($mount, '/');
		$this->backend = $backend;
		$this->storage = $storage;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ImageStack\Mount\MountInterface::mountImage()
	 */
	public function mountImage($path) {
		if (! ($image = $this->backend->getImage($path))) return false;
		$this->storage->storeImage($image, $this->mount, $path);
		return $image;
	}
	
}