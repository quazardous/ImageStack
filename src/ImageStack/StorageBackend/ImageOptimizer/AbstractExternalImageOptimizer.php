<?php
namespace ImageStack\StorageBackend\ImageOptimizer;

use ImageStack\OptionnableTrait;
use ImageStack\Api\ImageInterface;
use ImageStack\StorageBackend\Exception\StorageBackendException;

/**
 * Abstract class to handle external optimizer (like jpegtran).
 */
abstract class AbstractExternalImageOptimizer implements ImageOptimizerInterface
{
    use OptionnableTrait;
    
    /**
     * External image optimizer constructor.
     * @param array $options
     * Options:
     *  - tempnam_prefix : prefix for tmp files (default: 'eio')
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }
    
    /**
     * Return the correct output file extension.
     * @return string
     */
    abstract protected function getInputFileExtension();
    
	/**
	 * Get a non existing tempname.
	 * @param string $variation
	 * @return string
	 */
	protected function getTempnam($variation, $extention) {
	    $prefix = $this->getOption('tempnam_prefix', 'eio') . $variation;
		// tempnam() does not handle extension
		while (true) {
			$filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $prefix . substr(md5(uniqid().rand()), 0, 8) . '.' . $extention;
			if (!is_file($filename)) {
				return $filename;
			}
		}
	}

	/**
	 * Execute the external optimizer.
	 * @param string $if input file (should be OK before exec)
	 * @param string $binaryContent output binary content
	 * @return string the MIME type of the generated file
	 */
	abstract protected function execExternalOptimizer($if, &$binaryContent);
	
	/**
	 * {@inheritDoc}
	 * @see \ImageStack\StorageBackend\ImageOptimizer\ImageOptimizerInterface::optimizeImage()
	 * @throws StorageBackendException
	 */
	public function optimizeImage(ImageInterface $image) {
	    $if = $this->getTempnam("if", $this->getInputFileExtension());
		if (!file_put_contents($if, $image->getBinaryContent())) {
			throw new StorageBackendException(sprintf('Cannot write tmpfile : %s', $if), StorageBackendException::CANNOT_WRITE_TMPFILE);
		}
		$binaryContent = null;
		$mimeType = $this->execExternalOptimizer($if, $binaryContent);
		unlink($if);
		$image->setBinaryContent($binaryContent, $mimeType);
	}
}