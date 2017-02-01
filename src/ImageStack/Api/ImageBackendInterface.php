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
	 * @throws Exception\ImageNotFoundException to notify image not found
	 * 
	 */
	public function fetchImage(ImagePathInterface $path);
}