<?php
namespace ImageStack;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\StorageBackendInterface;
use ImageStack\Api\ImageInterface;

class ImageStack
{
    /**
     * @var ImageBackendInterface
     */
    protected $imageBackend;
    
    /**
     * @var StorageBackendInterface
     */
    protected $storageBackend;
    
    /**
     * Image stack constructor.
     * @param ImageBackendInterface $imageBackend
     * @param StorageBackendInterface $storageBackend
     * NB: storage backend is optional
     */
    public function __construct(ImageBackendInterface $imageBackend, StorageBackendInterface $storageBackend = null)
    {
        $this->imageBackend = $imageBackend;
        $this->storageBackend = $storageBackend;
    }
    
    /**
     * Stacks the image for the given path
     * @param unknown $path
     * @return ImageInterface
     */
	public function stackImage($path) {
		if (! ($image = $this->imageBackend->fetchImage($path))) return false;
		if ($this->storageBackend) {
		    $this->storageBackend->persistImage($image, $path);
		}
		return $image;
	}
}