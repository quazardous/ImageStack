<?php
namespace ImageStack\ImageBackend;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\Image;
use ImageStack\Api\ImageManipulatorInterface;

/**
 * Manipulator image backend.
 * Add image manipulator in the image backend layer.
 */
class ManipulatorImageBackend implements ImageBackendInterface
{
    /**
     * @var ImageBackendInterface
     */
    protected $imageBackend;
    
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
     * Manipulator image backend constructor.
     * @param ImageBackendInterface $imageBackend
     * @param ImageManipulatorInterface[] $imageManipulators
     */    
    public function __construct(ImageBackendInterface $imageBackend, array $imageManipulators = [])
    {
        $this->imageBackend = $imageBackend;
        foreach ($imageManipulators as $imageManipulator) {
            $this->addImageManipulator($imageManipulator);
        }
    }

    public function fetchImage(ImagePathInterface $path)
    {
        $image = $this->imageBackend->fetchImage($path);
		foreach ($this->imageManipulators as $imageManipulator) {
		    $imageManipulator->manipulateImage($image, $path);
		}
        return $image;
    }
    
}

