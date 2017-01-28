<?php
namespace ImageStack\StorageBackend;

use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\StorageBackend\ImageOptimizer\ImageOptimizerInterface;

class OptimizedFileStorageBackend extends FileStorageBackend {

    /**
     * Optimized file storage backend constructor.
     * This backend will use the given optimizers to write optimized image to the FS.
     * @param string $root the root folder to write images
     * @param ImageOptimizerInterface[] $imageOptimizers
     * Exemple : [
     *    'image/jpeg' => new JpegImageOptimizer(),
     *    'default' => new DefaultImageOptimize(), // you can specify a default optimizer
     * ]
     * @param array $options
     * @see FileStorageBackend::__construct()
     */
	public function __construct($root, array $imageOptimizers = [], $options = []) {
        foreach ($imageOptimizers as $mimeType => $imageOptimizer) {
            $this->registerImageOptimizer($mimeType, $imageOptimizer);
        }
	    parent::__construct($root, $options);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \ImageStack\StorageBackend\FileStorageBackend::storeImage()
	 */
	public function storeImage(ImageInterface $image, ImagePathInterface $path) {
		$this->optimizeImage($image);
		$this->writeImageFile($image, $path);
	}
	
	/**
	 * @var ImageOptimizerInterface[]
	 */
	protected $imageOptimizers = [];
	
	/**
	 * Register an image optimzer for the given MIME type.
	 * @param string $mimeType
	 * @param ImageOptimizerInterface $imageOptimizer
	 */
	public function registerImageOptimizer($mimeType, ImageOptimizerInterface $imageOptimizer = null)
	{
	    if (empty($imageOptimizer)) {
	        unset($this->imageOptimizers[$mimeType]);
	    } else {
	        $this->imageOptimizers[$mimeType] = $imageOptimizer;
	    }
	}
	
	/**
	 * Call optimizer by MIME type.
	 * If ther is no corresponding MIME type, it tries with a default optimizer.
	 * @param ImageInterface $image
	 */
    protected function optimizeImage(ImageInterface $image)
    {
        /**
         * @var ImageOptimizerInterface $optimizer
         */
        $optimizer = null;
        if (isset($this->imageOptimizers[$image->getMimeType()])) {
            $optimizer = $this->imageOptimizers[$image->getMimeType()];
        } elseif (isset($this->imageOptimizers['default'])) {
            $optimizer = $this->imageOptimizers['default'];
        }
        if ($optimizer) {
            $optimizer->optimizeImage($image);
        }
    }
}