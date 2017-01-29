<?php
namespace ImageStack\ImageOptimizer;

use ImageStack\Api\ImageInterface;

interface ImageOptimizerInterface
{
    /**
     * Optimize the image.
     * @param ImageInterface $image
     */
    public function optimizeImage(ImageInterface $image);
}