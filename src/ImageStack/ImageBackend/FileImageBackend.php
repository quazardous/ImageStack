<?php
namespace ImageStack\ImageBackend;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Image;
use ImageStack\Api\ImagePathInterface;
use ImageStack\OptionnableTrait;
use ImageStack\ImageBackend\Exception\ImageBackendException;

/**
 * File image backend.
 * Get images from the FS.
 */
class FileImageBackend implements ImageBackendInterface {
	use OptionnableTrait;
	
	/**
	 * File image backend constructor.
	 * @param string $root the root folder to write images
	 * @param array $options
	 */
	public function __construct($root, $options = array()) {
	    if (empty($root)) {
	        throw new \InvalidArgumentException('root cannot be empty');
	    }
		$this->setOptions($options);
		$this->setOption('root', $root);
	  
	}
	
	/**
	 * Get the image filename.
	 * @param ImagePathInterface $path
	 * @return string
	 */
	protected function getImageFilename(ImagePathInterface $path) {
	  return sanitize_path(implode(DIRECTORY_SEPARATOR, [
	      $this->getOption('root'),
	      $path->getPath(),
	  ]));
	}
	
    public function fetchImage(ImagePathInterface $path)
    {
        $filename = $this->getImageFilename($path);
        if (!is_file($filename)) {
            throw new ImageBackendException(sprintf('Image Not Found : %s', $filename), ImageBackendException::IMAGE_NOT_FOUND);
        }
        if (false === ($binaryData = @file_get_contents($filename))) {
            throw new ImageBackendException(sprintf('Cannot read file : %s', $filename), ImageBackendException::CANNOT_READ_FILE);
        }
        return new Image($binaryData);
    }

}