<?php
namespace ImageStack\Api;

use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;

/**
 * API image manipulator interface.
 *
 */
interface ImageManipulatorInterface
{

    /**
     * Manipulate image.
     * @param ImageInterface $image
     * @param ImagePathInterface $path
     */
    public function manipulateImage(ImageInterface $image, ImagePathInterface $path);
    
}