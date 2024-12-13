<?php
namespace ImageStack\ImageOptimizer;

use ImageStack\Api\ImageInterface;

/**
 * Optimize image.
 *
 */
interface ImageOptimizerInterface
{
    /**
     * Optimize the image.
     * @param ImageInterface $image
     */
    public function optimizeImage(ImageInterface $image);
    
    /**
     * Return an array with the supported MIME types.
     * @return string[]
     */
    public function getSupportedMimeTypes();
}
