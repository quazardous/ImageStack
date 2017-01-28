<?php
namespace ImageStack\StorageBackend\ImageOptimizer;

use ImageStack\StoageBackend\Exception\StorageBackendException;

/**
 * Pngcrush image optimizer.
 */
class JpegtranImageOptimizer extends AbstractExternalImageOptimizer
{

    /**
     * {@inheritDoc}
     * @see \ImageStack\StorageBackend\ImageOptimizer\AbstractExternalImageOptimizer::getInputFileExtension()
     */
    protected function getInputFileExtension()
    {
        return 'jpg';
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\StorageBackend\ImageOptimizer\AbstractExternalImageOptimizer::execExternalOptimizer()
     */
	protected function execExternalOptimizer($if, &$binaryContent)
	{
	    $cmd = [
	        $this->getOption('jpegtran', 'jpegtran'),
	        $this->getOption('jpegtran_options', '-copy none -optimize'),
	        $if,
	    ];
	    $ret = null;
	    ob_start();
	    passthru(implode(' ', $cmd), $ret);
	    $binaryContent = ob_get_clean();
    	if ($ret !== 0) {
			throw new StorageBackendException(sprintf('Exec error jpegtran (%d)', $ret), StorageBackendException::EXEC_ERROR);
		}
	    return 'image/jpeg';
	}
	
}