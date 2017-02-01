<?php
namespace ImageStack\ImageManipulator;

use ImageStack\Api\ImageManipulatorInterface;
use ImageStack\ImagineAwareTrait;
use Imagine\Image\ImagineInterface;
use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\ImageWithImagineInterface;
use ImageStack\ImageManipulator\ThumbnailRule\ThumbnailRuleInterface;
use ImageStack\ImagineAwareInterface;
use ImageStack\ImageManipulator\Exception\ImageManipulatorException;

/**
 * Thumbnailer image manipulator.
 * Resize the image using rules.
 * 
 * Handles only ImageWithImagineInterface images. 
 *
 */
class ThumbnailerImageManipulator implements ImageManipulatorInterface {
    use ImagineAwareTrait;
    
	public function __construct(ImagineInterface $imagine, array $thumbnailRules = [])
	{
	    $this->setImagine($imagine);
        foreach ($thumbnailRules as $thumbnailRule) {
            $this->addThumbnailRule($thumbnailRule);
        }
	}
	
	/**
	 * @var ThumbnailRuleInterface[]
	 */
	protected $thumbnailRules = [];

	/**
	 * Add a thumbnail rule.
	 * @param ThumbnailRuleInterface $thumbnailRule
	 */
	public function addThumbnailRule(ThumbnailRuleInterface $thumbnailRule)
	{
	    if ($thumbnailRule instanceof ImagineAwareInterface) {
	        // not very LSP but handy
	        if (!$thumbnailRule->getImagine()) {
	           $thumbnailRule->setImagine($this->getImagine());
	        }
	    }
	    $this->thumbnailRules[] = $thumbnailRule;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \ImageStack\Api\ImageManipulatorInterface::manipulateImage()
	 */
	public function manipulateImage(ImageInterface $image, ImagePathInterface $path) {
	    $this->_manipulateImage($image, $path);
	}

    /**
     * Ensure use of ImageWithImagineInterface.
     * 
     * @param ImageWithImagineInterface $image            
     * @param ImagePathInterface $path            
     */
    protected function _manipulateImage(ImageWithImagineInterface $image, ImagePathInterface $path)
    {
        foreach ($this->thumbnailRules as $thumbnailRule) {
            if ($thumbnailRule->thumbnailImage($image, $path)) {
                // successfully return at first match
                return;
            }
        }
        // default is to throw an error if no match.
        // End with an always matching rule to bypass.
        throw new ImageManipulatorException(sprintf('Cannot manipulate image: %s', $path->getPath()), ImageManipulatorException::CANNOT_MANIPULATE_IMAGE);
    }
	
}