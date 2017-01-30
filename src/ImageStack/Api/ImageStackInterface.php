<?php
namespace ImageStack\Api;

use ImageStack\Api\ImagePathInterface;
use ImageStack\Api\ImageInterface;

/**
 * API image stack inteface.
 * It is the top of the iceberg interface.
 * It is in charge of pulling together all the stuff.
 * 
 * The typical stackImage() flow is:
 * - fetch image with ImageBackendInterface::fetchImage($path)
 * - [optional] modify image with ImageManipulatorInterface::manipulateImage($image, $path)
 * - [optional] store image with StorageBackendInterface::storeImage($image, $path)
 * 
 */
interface ImageStackInterface
{
    /**
     * Stack image and return it.
     * @param ImagePathInterface $path
     * @return ImageInterface $image
     */
    public function stackImage(ImagePathInterface $path);
    
}