<?php
namespace ImageStack\Api;

/**
 * Image backend represents the source of the images. Can be the FS or a distant server.
 *
 */
interface ImageBackendInterface {
	
	/**
	 * Fetch the image from the image backend
	 * 
	 * @param ImagePathInterface $path
	 * 
	 * @return ImageInterface|false
	 * NB: returns false when the image was not found (404).
	 * 
	 * @throws Exception\ImageBackendException if something goes wrong other than 404
	 */
	public function fetchImage(ImagePathInterface $path);
}