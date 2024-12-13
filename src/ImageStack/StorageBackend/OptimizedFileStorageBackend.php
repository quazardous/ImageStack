<?php

namespace ImageStack\StorageBackend;

use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\ImageOptimizer\ImageOptimizerInterface;
use ImageStack\ImageManipulator\OptimizerImageManipulator;

/**
 * Optimized file storage backend.
 * 
 * NB: shortcut for FileStorageBackend + OptimizerImageManipulator
 */
class OptimizedFileStorageBackend extends FileStorageBackend {

    protected $optimizerImageManipulator;
    
    /**
     * Optimized file storage backend constructor.
     * This backend will use the given optimizers to write optimized image to the FS.
     * @param string $root the root folder to write images
     * @param ImageOptimizerInterface[] $imageOptimizers
     * @param array $options
     * @see FileStorageBackend::__construct()
     */
    public function __construct($root, array $imageOptimizers = [], $options = []) {
        $this->optimizerImageManipulator = new OptimizerImageManipulator($imageOptimizers);
        parent::__construct($root, $options);
    }
    
    /**
     * {@inheritDoc}
     * @see \ImageStack\StorageBackend\FileStorageBackend::storeImage()
     */
    public function storeImage(ImageInterface $image, ImagePathInterface $path) {
        $this->optimizerImageManipulator->manipulateImage($image, $path);
        $this->writeImageFile($image, $path);
    }
    
    /**
     * Register an image optimizer.
     * @param string $mimeType
     * @param ImageOptimizerInterface $imageOptimizer
     */
    public function registerImageOptimizer(ImageOptimizerInterface $imageOptimizer)
    {
        $this->optimizerImageManipulator->registerImageOptimizer($imageOptimizer);
    }
}
