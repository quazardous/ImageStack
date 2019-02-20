<?php

namespace ImageStack\ImageManipulator;

use ImageStack\ImageWithImagineInterface;

trait AnimatedGifAwareImageManipulatorTrait
{
    protected function handleAnimatedGif(ImageWithImagineInterface $image, callable $initCallback = null, callable $frameCallback = null, callable $completeCallback = null)
    {
        if ($image->getMimeType() != 'image/gif') return false;
        if (! $image->getImagine() instanceof \Imagine\Imagick\Imagine) return false;
        if (empty($image->getImagineImage()->layers())) return false;
        if ($image->getImagineImage()->layers()->count() <= 1) return false;
        
        $imagineImage = $image->getImagineImage();
        
        if ($initCallback) call_user_func($initCallback, $image->getImagineImage());
        
        if ($frameCallback) {
            $imagineImage->layers()->coalesce();
            foreach ($imagineImage->layers() as $frame) {
                call_user_func($frameCallback, $frame);
            }
        }
        
        if ($completeCallback) call_user_func($completeCallback, $imagineImage);
    }
}