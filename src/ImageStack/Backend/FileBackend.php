<?php
namespace ImageStack\Backend;

use ImageStack\Image;
use ImageStack\OptionableComponent;

/**
 * Backend fichier.
 * Récupère l'image qui correspond au chemin sur le système de fichiers.
 *
 */
class FileBackend extends OptionableComponent implements BackendInterface {
	
	/**
	 * @param array $options
	 * @throws \BadMethodCallException
	 */
	public function __construct($options = array()) {
		
		if (!is_array($options)) {
			$options = array('backend_dir' => $options);
		}
		
		parent::__construct($options);
		
		if (!isset($this->options['backend_dir'])) {
			throw new \InvalidArgumentException("missing 'backend_dir' option");
		}
		$this->options['backend_dir'] = rtrim($this->options['backend_dir'], '/');
	  
	}
	
	protected function getFilename($path) {
	  return $this->options['backend_dir'] . '/' . ltrim($path, '/');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ImageStack\Backend\BackendInterface::getImage()
	 */
	public function getImage($path) {
		$filename = $this->getFilename($path);
		$data = @file_get_contents($filename);
		if ($data === false) return false;
		$this->app->log($this->getName(), "$filename >");
		$type = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if ($type == 'jpg') {
			$type = 'jpeg';
		}
		
		try {
		  $image = new Image($this->app['imagine'], $type, $data);
		}
		catch (\Exception $e) {
		  return false;
		}
		
		return $image;
	}
}