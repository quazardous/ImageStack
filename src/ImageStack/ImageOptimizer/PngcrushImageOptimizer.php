<?php
namespace ImageStack\ImageOptimizer;

use ImageStack\ImageOptimizer\Exception\ImageOptimizerException;

/**
 * Pngcrush image optimizer.
 */
class PngcrushImageOptimizer extends AbstractExternalImageOptimizer
{
    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\ImageOptimizerInterface::getSupportedMimeTypes()
     */
    public function getSupportedMimeTypes()
    {
        return [
            'image/png',
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\AbstractExternalImageOptimizer::getInputFileExtension()
     */
    protected function getInputFileExtension()
    {
        return 'png';
    }

    /**
     * Return the output file extension.
     * @return string
     */
    protected function getOutputFileExtension()
    {
        return 'png';
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\AbstractExternalImageOptimizer::execExternalOptimizer()
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
			throw new ImageOptimizerException(sprintf('Exec error pngcrush (%d): %s', $ret, implode("\n", $output)), ImageOptimizerException::EXEC_ERROR);
		}
		if (false === ($binaryContent = file_get_contents($of))) {
			throw new ImageOptimizerException(sprintf('Cannot read tmpfile : %s', $of), ImageOptimizerException::CANNOT_READ_TMPFILE);
		}
		unlink($of);
	    return 'image/png';
	}
	
}