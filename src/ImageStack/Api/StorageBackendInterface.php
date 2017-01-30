<?php
namespace ImageStack\Api;

/**
 * API storage backend interface.
 * Storage backend is responsible of persisting the image on a (local) backend.
 * A typical backend is the file system (FS): at the next image HTTP request, the image will be serve statically (without ImageStack).
 *
 */
interface StorageBackendInterface {
	/**
	 * Store the image.
	 * @param ImageInterface $image to persist
	 * @param ImagePathInterface $path
	 * NB: If the image is modified before storage, the image object should reflect those modifications.
	 * This ensure that the controller layer will serve the actual image.
	 */
	public function storeImage(ImageInterface $image, ImagePathInterface $path);
}