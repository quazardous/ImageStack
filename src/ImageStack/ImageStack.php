<?php
namespace ImageStack;

use ImageStack\Api\ImageStackInterface;
use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\StorageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImageManipulatorInterface;

/**
 * Image stack implementation.
 * 
 * Image backend is mandatory.
 * This image stack can sequentially apply an array of image manipulators.
 * Storage backend is optionnal.
 */
class ImageStack implements ImageStackInterface
{
    /**
     * @var ImageBackendInterface
     */
    protected $imageBackend;
    
    /**
     * Set the image backend.
     * @param ImageBackendInterface $imageBackend
     */
    public function setImageBackend(ImageBackendInterface $imageBackend)
    {
        $this->imageBackend = $imageBackend;
    }
    
    /**
     * @var StorageBackendInterface
     */
    protected $storageBackend;
    
    /**
     * Set the storage backend.
     * @param StorageBackendInterface $storageBackend
     */
    public function setStorageBackend(StorageBackendInterface $storageBackend = null)
    {
        $this->storageBackend = $storageBackend;
    }
    
    /**
     * @var ImageManipulatorInterface[]
     */
    protected $imageManipulators = [];
    
    /**
     * Add image manipulator.
     * @param ImageManipulatorInterface $storageBackend
     */
    public function addImageManipulator(ImageManipulatorInterface $imageManipulator)
    {
        $this->imageManipulators[] = $imageManipulator;
    }
    
    /**
     * Image stack constructor.
     * @param ImageBackendInterface $imageBackend
     * @param StorageBackendInterface $storageBackend
     * @param ImageManipulatorInterface[] $imageManipulators
     */
    public function __construct(ImageBackendInterface $imageBackend, StorageBackendInterface $storageBackend = null, array $imageManipulators = [])
    {
        $this->setImageBackend($imageBackend);
        $this->setStorageBackend($storageBackend);
        foreach ($imageManipulators as $imageManipulator) {
            $this->addImageManipulator($imageManipulator);
        }
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\Api\ImageStackInterface::stackImage()
     * @return ImageInterface
     */
	public function stackImage(ImagePathInterface $path) {
		$image = $this->imageBackend->fetchImage($path);
		foreach ($this->imageManipulators as $imageManipulator) {
		    $imageManipulator->manipulateImage($image, $path);
		}
		if ($this->storageBackend) {
		    $this->storageBackend->storeImage($image, $path);
		}
		return $image;
	}
}