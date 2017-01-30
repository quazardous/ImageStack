<?php
namespace ImageStack\Api;

/**
 * API image backend interface.
 * Image backend represents the source of the images. Can be the FS or a distant server.
 *
 */
interface ImageBackendInterface {
	
	/**
	 * Fetch the image from the image backend
	 * @param ImagePathInterface $path
	 * @return ImageInterface
	 * 
	 * @throws Exception\ImageBackendException
	 * 
	 * For 404 Not Found implementation must throw a Exception\ImageBackendException
	 * with code Exception\ImageBackendException::IMAGE_NOT_FOUND.
	 */
	public function fetchImage(ImagePathInterface $path);
}