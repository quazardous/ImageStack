<?php
namespace ImageStack\Manipulator;

use ImageStack\Image;
use ImageStack\OptionableComponent;

class ThumbnailerManipulator extends OptionableComponent implements ManipulatorInterface {

	public function __construct($options = array()) {
		
		parent::__construct($options);
		
		if (!isset($this->options['profiles'])) {
			throw new \InvalidArgumentException("missing 'profiles' option");
		}
	}
	
	public function manipulate(Image $image, $path) {
		foreach ($this->options['profiles'] as $pattern => $format) {
		  // si un pattern est à false on veut un 404
		  if (false === $format) return false;
		  	  
		  // si un pattern est à true on veut l'originale
		  if (true === $format) return true;
		  
			if (preg_match($pattern, $path, $matches)) {
			  
			  if (is_numeric($format)) {
			    // si numeric on veut juste du reporting et 404
			    $this->app['logger']->info(sprintf("%s Unhandled [%s]", $this->getName(), $matches[$format]));
			    return false;
			  }
			  
				$this->thumbnail($image, $format);
				return true;
			}
		}
		// par défaut on laisse passer l'image originale
		return true;
	}
	
	protected function thumbnail(Image $image, $format) {
		if (preg_match('/^(\<)?([0-9]+)x([0-9]+)$/', $format, $matches)) {
			$size = new \Imagine\Image\Box($matches[2], $matches[3]);
			$mode = $matches[1] == '<' ? \Imagine\Image\ImageInterface::THUMBNAIL_INSET : \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
		} elseif (preg_match('/^(\<)?([0-9]+)$/', $format, $matches)) {
			$size = new \Imagine\Image\Box($matches[2], $matches[2]);
			$mode = $matches[1] == '<' ? \Imagine\Image\ImageInterface::THUMBNAIL_INSET : \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
		}	else {
			throw new \InvalidArgumentException(sprintf("%s incorrect format", $format));
		}
		$im = $image->getImage();
		$thumbnail = $im->thumbnail($size, $mode);
		$image->setData($thumbnail->get($image->getType()));
		$this->app['logger']->info($this->getName()." $format =");
	}
}