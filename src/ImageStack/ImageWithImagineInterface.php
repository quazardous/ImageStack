<?php
namespace ImageStack;

use ImageStack\Api\ImageInterface;
use Imagine\Image\ImageInterface as ImagineImageInterface;

/**
 * Image that can handle imagine image interface.
 * Binary content is lazy managed when possible because it is time/CPU consuming.
 *
 */
interface ImageWithImagineInterface extends ImageInterface, ImagineAwareInterface
{
    /**
     * Set imagine options for binary content generation.
     * @param array $imagineOptions
     */
    public function setImagineOptions(array $imagineOptions);

    /**
	 * Return an imagine image object from the image binary content.
	 * @throws ImageException
	 * @return ImagineImageInterface
	 */
    public function getImagineImage();
    
    /**
     * Set an imagine image to replace the binary content.
     * @param ImagineImageInterface $imagineImage
     * @param string $mimeType to use for binary content generation
     * 
     * The implementation should deprecate the binary content if needed.
     * 
     * @see ImageInterface::getBinaryContent()
     */
    public function setImagineImage(ImagineImageInterface $imagineImage, $mimeType = null);

    /**
     * Set MIME type for binary content generation.
     * The implementation should deprecate the binary content if MIME type changes.
     * @param string $mimeType
     */
    public function setMimeType($mimeType);
    
    /**
     * Deprecate binary content.
     * ie: notify that imagine image has been modified externally.
     */
    public function deprecateBinaryContent();
    
}
