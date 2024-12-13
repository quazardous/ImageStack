<?php

namespace ImageStack\ImageOptimizer;

use ImageStack\ImageOptimizer\Exception\ImageOptimizerException;

/**
 * Pngcrush image optimizer.
 */
class JpegtranImageOptimizer extends AbstractExternalImageOptimizer
{

    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\ImageOptimizerInterface::getSupportedMimeTypes()
     */
    public function getSupportedMimeTypes()
    {
        return [
            'image/jpeg',
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\AbstractExternalImageOptimizer::getInputFileExtension()
     */
    protected function getInputFileExtension()
    {
        return 'jpg';
    }
    
    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\AbstractExternalImageOptimizer::checkIsMimeTypeSupported()
     */
    protected function checkIsMimeTypeSupported($mimeType)
    {
        return 'image/jpeg' == $mimeType;
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\ImageOptimizer\AbstractExternalImageOptimizer::execExternalOptimizer()
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
            throw new ImageOptimizerException(sprintf('Exec error jpegtran (%d)', $ret), ImageOptimizerException::EXEC_ERROR);
        }
        return 'image/jpeg';
    }
    
}
