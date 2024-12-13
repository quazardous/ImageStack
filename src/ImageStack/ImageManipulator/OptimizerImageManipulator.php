<?php
namespace ImageStack\ImageManipulator;

use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\ImageOptimizer\ImageOptimizerInterface;
use ImageStack\Api\ImageManipulatorInterface;

/**
 * Optimizer image manipulator.
 * Allow to register many image optimizer by MIME type.
 *
 */
class OptimizerImageManipulator implements ImageManipulatorInterface {

    /**
     * Optimizer image manipulator constructor.
     * @param ImageOptimizerInterface[] $imageOptimizers
     */
    public function __construct(array $imageOptimizers = []) {
        foreach ($imageOptimizers as $imageOptimizer) {
            $this->registerImageOptimizer($imageOptimizer);
        }
    }
    
    /**
     * @var ImageOptimizerInterface[][]
     */
    protected $imageOptimizers = [];
    
    /**
     * Register an image optimizer.
     * @param string $mimeType
     * @param ImageOptimizerInterface $imageOptimizer
     */
    public function registerImageOptimizer(ImageOptimizerInterface $imageOptimizer)
    {
        foreach ($imageOptimizer->getSupportedMimeTypes() as $mimeType) {
            $this->imageOptimizers[$mimeType][] = $imageOptimizer;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \ImageStack\Api\ImageManipulatorInterface::manipulateImage()
     */
    public function manipulateImage(ImageInterface $image, ImagePathInterface $path)
    {
        /**
         * @var ImageOptimizerInterface $optimizer
         */
        $optimizer = null;
        if (!empty($this->imageOptimizers[$image->getMimeType()])) {
            // Use the first matching optimizer.
            $optimizer = reset($this->imageOptimizers[$image->getMimeType()]);
        }
        if ($optimizer) {
            $optimizer->optimizeImage($image);
        }
    }
    
}