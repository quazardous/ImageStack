<?php
namespace ImageStack\Storage;

use ImageStack\Image;
use ImageStack\OptionableComponent;

class FileStorage extends OptionableComponent implements StorageInterface {
	
	public function __construct($options = array()) {
		
		if (!is_array($options)) {
			$options = array('root' => $options);
		}
		
		parent::__construct($options);
		
		if (!isset($this->options['root'])) {
			throw new \InvalidArgumentException("missing 'root' option");
		}
	}
	
	public function storeImage(Image $image, $mount, $path) {
		$data = &$image->getData();
		$this->writeData($data, $mount . '/' . ltrim($path, '/'));
	}
	
	protected function writeData($data, $path) {
		$filename = $this->options['root'] . '/' .ltrim($path, '/');
		$dirname = dirname($filename);
		if (!is_dir($dirname)) {
			@mkdir($dirname, 0755, true);
		}
		if (!file_put_contents($filename, $data)) {
			throw new \RuntimeException("Error : cannot write $filename");
		}
		$this->app['logger']->info($this->getName()." $filename <");
	}
}
  