<?php
namespace ImageStack\Manipulator;

use ImageStack\Image;

interface ManipulatorInterface {
	
	/**
	 * Manipulate the image.
	 * @param Image $image
	 * @param string $path
	 * @return boolean
	 * @throws \RuntimeException
	 */
	public function manipulate(Image $image, $path);
	
}