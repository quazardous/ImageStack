<?php
namespace ImageStack;

use ImageStack\Api\ImageInterface;
use ImageStack\Exception\ImageException;

/**
 * Basic image implementation.
 */
class Image implements ImageInterface
{

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $binaryContent;

    /**
     * Image constructor.
     * This implementation tries to guess the MIME type.
     * @param string $binaryContent      
     * @param string $mimeType      
     */
    public function __construct($binaryContent, $mimeType = null)
    {
        $this->setBinaryContent($binaryContent, $mimeType);
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\Api\ImageInterface::setBinaryContent()
     */
    public function setBinaryContent($binaryContent, $mimeType = null)
    {
        if (empty($binaryContent)) {
            throw new ImageException('Empty image', ImageException::EMPTY_IMAGE);
        }
        $this->binaryContent = $binaryContent;
        $this->mimeType = $mimeType;
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\Api\ImageInterface::getBinaryContent()
     */
    public function getBinaryContent()
    {
        return $this->binaryContent;
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\Api\ImageInterface::getMimeType()
     * @throws ImageException
     */
    public function getMimeType()
    {
        if (empty($this->mimeType)) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $this->mimeType = $finfo->buffer($this->binaryContent);
            if (empty($this->mimeType)) {
                throw new ImageException('Cannot determine MIME type', ImageException::CANNOT_DETERMINE_MIME_TYPE);
            }
        }
        return $this->mimeType;
    }
    
}