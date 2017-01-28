<?php
namespace ImageStack\StorageBackend\ImageOptimizer;

use ImageStack\StorageBackend\Exception\StorageBackendException;

/**
 * Pngcrush image optimizer.
 */
class PngcrushImageOptimizer extends AbstractExternalImageOptimizer
{

    /**
     * {@inheritDoc}
     * @see \ImageStack\StorageBackend\ImageOptimizer\AbstractExternalImageOptimizer::getInputFileExtension()
     */
    protected function getInputFileExtension()
    {
        return 'png';
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\StorageBackend\ImageOptimizer\AbstractExternalImageOptimizer::getOutputFileExtension()
     */
    protected function getOutputFileExtension()
    {
        return 'png';
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\StorageBackend\ImageOptimizer\AbstractExternalImageOptimizer::execExternalOptimizer()
     */
	protected function execExternalOptimizer($if, &$binaryContent)
	{
		$of = $this->getTempnam("of", $this->getOutputFileExtension());
	    $cmd = [
	        $this->getOption('pngcrush', 'pngcrush'),
	        $this->getOption('pngcrush_options', '-q -rem allb -brute -reduce'),
	        $if,
	        $of,
	    ];
	    $output = [];
	    $ret = null;
	    exec(implode(' ', $cmd) . ' 2>&1', $output, $ret);
    	if ($ret !== 0) {
			throw new StorageBackendException(sprintf('Exec error pngcrush (%d): %s', $ret, implode("\n", $output)), StorageBackendException::EXEC_ERROR);
		}
		if (false === ($binaryContent = file_get_contents($of))) {
			throw new StorageBackendException(sprintf('Cannot read tmpfile : %s', $of), StorageBackendException::CANNOT_READ_TMPFILE);
		}
		unlink($of);
	    return 'image/png';
	}
	
}