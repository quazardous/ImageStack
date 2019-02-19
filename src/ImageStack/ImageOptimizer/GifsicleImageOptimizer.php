<?php
namespace ImageStack\ImageOptimizer;

use ImageStack\ImageOptimizer\Exception\ImageOptimizerException;

/**
 * Gifsicle image optimizer.
 */
class GifsicleImageOptimizer extends AbstractExternalImageOptimizer
{

    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\ImageOptimizerInterface::getSupportedMimeTypes()
     */
    public function getSupportedMimeTypes()
    {
        return [
            'image/gif',
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\AbstractExternalImageOptimizer::getInputFileExtension()
     */
    protected function getInputFileExtension()
    {
        return 'gif';
    }
    
    /**
     * Return the output file extension.
     * @return string
     */
    protected function getOutputFileExtension()
    {
        return 'gif';
    }
    
    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\AbstractExternalImageOptimizer::checkIsMimeTypeSupported()
     */
    protected function checkIsMimeTypeSupported($mimeType)
    {
        return 'image/gif' == $mimeType;
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\AbstractExternalImageOptimizer::execExternalOptimizer()
     */
	protected function execExternalOptimizer($if, &$binaryContent)
	{
	    $of = $this->getTempnam("of", $this->getOutputFileExtension());
	    $cmd = [
	        $this->getOption('gifsicle', 'gifsicle'),
	        $this->getOption('gifsicle_options', '-O3'),
	        $if,
	        '-o',
	        $of,
	    ];
	    $output = [];
	    $ret = null;
	    exec(implode(' ', $cmd) . ' 2>&1', $output, $ret);
	    if ($ret !== 0) {
	        throw new ImageOptimizerException(sprintf('Exec error gifsicle (%d): %s', $ret, implode("\n", $output)), ImageOptimizerException::EXEC_ERROR);
	    }
	    if (false === ($binaryContent = file_get_contents($of))) {
	        throw new ImageOptimizerException(sprintf('Cannot read tmpfile : %s', $of), ImageOptimizerException::CANNOT_READ_TMPFILE);
	    }
	    unlink($of);
	    return 'image/gif';
	}
	
}