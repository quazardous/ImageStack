<?php
namespace ImageStack\Storage;

use ImageStack\Image;

interface StorageInterface {
	/**
	 * Store image
	 * @param \ImageStack\Image $image data can be modified (optimization)
	 * @param string $mount
	 * @param string $path
	 */
	public function storeImage(Image $image, $mount, $path);
}