<?php
namespace ImageStack\StorageBackend;

use ImageStack\Api\StorageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\Api\ImageInterface;
use ImageStack\OptionnableTrait;

use ImageStack\StoageBackend\Exception\StorageBackendException;

class FileStorageBackend implements StorageBackendInterface {
	use OptionnableTrait;
	
	/**
	 * File storage backend constructor.
	 * @param string $root the root folder to write images
	 * @param array $options
	 * Options can be :
	 *   - mode : mkdir mode (default 0755)
	 */
	public function __construct($root, $options = []) {
	    if (empty($root)) {
	        throw new \InvalidArgumentException('root cannot be empty');
	    }
		$this->setOptions($options);
		$this->setOption('root', $root);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \ImageStack\Api\StorageBackendInterface::storeImage()
	 */
	public function storeImage(ImageInterface $image, ImagePathInterface $path) {
		$this->writeImageFile($image->getBinaryContent(), $path);
	}
	
	/**
	 * Write image to FS.
	 * @param string $binaryContent
	 * @param ImagePathInterface $path
	 * @throws StorageBackendException
	 */
	protected function writeImageFile($binaryContent, ImagePathInterface $path) {
		$filename = sanitize_path(implode(DIRECTORY_SEPARATOR, [
		    $this->getOption('root'),
    		/**
    		 * The stack prefix is the part of the URL that is used to detect wich stack to trigger.
    		 * This implementation aims to store a file at the exact same URL so next requests could be served statically.
    		 */
		    $path->getStackPrefix(),
		    $path->getPath(),
		]));
		$dirname = dirname($filename);
		if (!is_dir($dirname)) {
			@mkdir($dirname, $this->getOption('mode', 0755), true);
		}
		if (!file_put_contents($filename, $binaryContent)) {
			throw new StorageBackendException(sprintf('Cannot write file %s', $filename), StorageBackendException::CANNOT_WRITE_FILE);
		}
	}
}
  