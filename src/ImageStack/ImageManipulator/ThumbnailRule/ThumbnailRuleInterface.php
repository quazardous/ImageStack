<?php
namespace ImageStack\ImageManipulator\ThumbnailRule;

use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;

/**
 * Thumbnail rule interface.
 *
 */
interface ThumbnailRuleInterface
{
    /**
     * Apply a conditional thumbnail transformation.
     * @param ImageInterface $image
     * @param ImagePathInterface $path
     * 
     * @return boolean true if the image was successfully turn into thumbnail
     */
    public function thumbnailImage(ImageInterface $image, ImagePathInterface $path);
}