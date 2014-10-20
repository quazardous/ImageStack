<?php
namespace ImageStack\Backend;

interface BackendInterface {
	
	/**
	 * Get the image from backend
	 * 
	 * @param string $path
	 * 
	 * @return \ImageStack\Image|false
	 *   returns false when the image was not found
	 * 
	 * @throws \RuntimeException something went wrong other than 404
	 */
	public function getImage($path);
}