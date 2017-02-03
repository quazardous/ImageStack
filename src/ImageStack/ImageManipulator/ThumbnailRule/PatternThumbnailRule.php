<?php
namespace ImageStack\ImageManipulator\ThumbnailRule;

use ImageStack\ImagineAwareInterface;
use ImageStack\ImagineAwareTrait;
use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\ImageWithImagineInterface;
use ImageStack\Api\Exception\ImageNotFoundException;
use ImageStack\ImageManipulator\ThumbnailRule\Exception\ThumbnailRuleException;

/**
 * Pattern thumbnail rule.
 *
 */
class PatternThumbnailRule implements ThumbnailRuleInterface, ImagineAwareInterface
{
    use ImagineAwareTrait;

    /**
     * A preg_match() pattern.
     * @var string
     */
    protected $pattern;
    
    /**
     * A thumbnail format/size.
     * @var string
     */
    protected $format;
    
    /**
     * Pattern thumbnail rule constructor.
     * @param string $pattern to match the path
     * @param boolean|string|callable $format thumbnail format
     * if format is a callable, it will be executed first with $matches as arg (the 3rd arg of the preg_match($pattern, $path, $matches)).
     * The final format can be:
     *  - (string) [<]<Width>x<Height>: a rectangle
     *  - (string) [<]<Length>: a square (shortcut for <Length>x<Length>)
     *    The optional '<' tells not to crop image (inset), default is to crop (outbound).
     *  - boolean: true means we wan the original image, false means throw a 404
     * formats examples:
     *  - 300x200: try to fit in the 300x200 box without cropping
     *  - <64: crop to fil in the 64x64 box
     */
    public function __construct($pattern, $format) {
        $this->pattern = $pattern;
        $this->format = $format;
    }
    
    /**
     * {@inheritdoc}
     *
     * @see \ImageStack\ImageManipulator\ThumbnailRule\ThumbnailRuleInterface::thumbnailImage()
     */
    public function thumbnailImage(ImageInterface $image, ImagePathInterface $path)
    {
        return $this->_thumbnailImage($image, $path);
    }
    
    /**
     * Ensure ImageWithImagineInterface.
     * @param ImageInterface $image
     * @param ImagePathInterface $path
     * @return boolean
     */
    protected function _thumbnailImage(ImageWithImagineInterface $image, ImagePathInterface $path)
    {
        $this->assertImagine();
        $matches = null;
        if (!preg_match($this->pattern, $path->getPath(), $matches)) {
            return false;
        }
        
        if (is_callable($this->format)) {
            $format = call_user_func($this->format, $matches);
        } else {
            $format = $this->format;
        }
        
        if (false === $format) {
            // force a not found
            throw new ImageNotFoundException(sprintf('Image not found: %s', $path->getPath()));
        }
        
        if (true === $format) {
            // we want the original image
            return true;
        }
        
        if (preg_match('/^(\<)?([0-9]+)x([0-9]+)$/', $format, $matches)) {
			$size = new \Imagine\Image\Box($matches[2], $matches[3]);
			$mode = $matches[1] == '<' ? \Imagine\Image\ImageInterface::THUMBNAIL_INSET : \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
		} elseif (preg_match('/^(\<)?([0-9]+)$/', $format, $matches)) {
			$size = new \Imagine\Image\Box($matches[2], $matches[2]);
			$mode = $matches[1] == '<' ? \Imagine\Image\ImageInterface::THUMBNAIL_INSET : \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
		} else {
			throw new ThumbnailRuleException(sprintf('Unsupported rule format: %s', (string)$format), ThumbnailRuleException::UNSUPPORTED_RULE_FORMAT);
		}
		$thumbnail = $image->getImagineImage()->thumbnail($size, $mode);
		$image->setImagineImage($thumbnail);
		return true;
    }
    
    /**
     * @throws ImageException
     */
    protected function assertImagine()
    {
        if (!$this->getImagine()) {
            throw new ThumbnailRuleException('Imagine not setup', ThumbnailRuleException::IMAGINE_NOT_SETUP);
        }
    }
}