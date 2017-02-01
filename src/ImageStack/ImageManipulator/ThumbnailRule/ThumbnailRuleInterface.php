<?php
namespace ImageStack\ImageManipulator\ThumbnailRule;

use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;

interface ThumbnailRuleInterface
{
    /**
     * Apply a condition thumbnail transformation.
     * @param ImageInterface $image
     * @param ImagePathInterface $path
     * 
     * @return boolean true if the image was successfully turn into thumbnail
     */
    public function thumbnailImage(ImageInterface $image, ImagePathInterface $path);
}