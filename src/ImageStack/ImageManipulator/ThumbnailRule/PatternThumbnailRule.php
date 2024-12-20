<?php
namespace ImageStack\ImageManipulator\ThumbnailRule;

use ImageStack\ImagineAwareInterface;
use ImageStack\ImagineAwareTrait;
use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\ImageWithImagineInterface;
use ImageStack\Api\Exception\ImageNotFoundException;
use ImageStack\ImageManipulator\ThumbnailRule\Exception\ThumbnailRuleException;
use ImageStack\Api\Exception\ImageException;
use Imagine\Imagick\Image as IImage;
use ImageStack\ImageManipulator\AnimatedGifAwareImageManipulatorTrait;

/**
 * Pattern thumbnail rule.
 *
 */
class PatternThumbnailRule implements ThumbnailRuleInterface, ImagineAwareInterface
{
    use ImagineAwareTrait;
    use AnimatedGifAwareImageManipulatorTrait;

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
     * A thumbnail filter.
     * @var string
     */
    protected $filter;
    
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
     * @param string $filter
     * @see \Imagine\Image\ImageInterface::FILTER_*
     */
    public function __construct($pattern, $format, $filter = \Imagine\Image\ImageInterface::FILTER_UNDEFINED) {
        $this->pattern = $pattern;
        $this->format = $format;
        $this->filter = $filter;
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
        
        if (is_null($format)) {
            // passthrough: we want the original image
            return true;
        }
        
        // not very LSP but handy
        if (!$image->getImagine()) {
            $image->setImagine($this->getImagine(), $this->getImagineOptions());
        }
        
        if (true === $format) {
            // we want the original image size but will trigger a save
            $image->deprecateBinaryContent();
            return true;
        }

        $formats = (array) $format;

        $handlers = [
            [$this, 'handlerCrop'],
            [$this, 'handlerZoom'],
            [$this, 'handlerCropCanvas'],
        ];

        foreach ($formats as $format) {
            $done = false;
            foreach ($handlers as $handler) {
                if (call_user_func($handler, $image, $format)) {
                    $done = true;
                    break;
                }
            }
            if (!$done) {
                throw new ThumbnailRuleException(sprintf('Unsupported rule format: %s', (string)$format), ThumbnailRuleException::UNSUPPORTED_RULE_FORMAT);
            }
        }

		return true;
    }

    protected function handlerCrop(ImageWithImagineInterface $image, $format)
    {
        if (preg_match('/^(\<)?([0-9]+)x([0-9]+)$/', $format, $matches)) {
            $size = new \Imagine\Image\Box($matches[2], $matches[3]);
            $mode = $matches[1] == '<' ? \Imagine\Image\ImageInterface::THUMBNAIL_INSET : \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        } elseif (preg_match('/^(\<)?([0-9]+)$/', $format, $matches)) {
            $size = new \Imagine\Image\Box($matches[2], $matches[2]);
            $mode = $matches[1] == '<' ? \Imagine\Image\ImageInterface::THUMBNAIL_INSET : \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        } else {
            return false;
        }
        
        /** @var IImage $animated */
        $animated = null;
        $imagine = $image->getImagine();
        if ($this->handleAnimatedGif($image, function (IImage $iimage) use ($imagine, $size, &$animated, $image) {
            $animated = $imagine->create($size);
            $animated->layers()->remove(0);
            $image->setImagineImage($animated);
            // we take first frame conf
            /** @var IImage $iframe */
            $iframe = $iimage->layers()[0];
            $options = [
                'flatten' => false,
                'animated' => true,
                'animated.delay' => $iframe->getImagick()->getImageDelay() * 10,
                'animated.loop' => $iframe->getImagick()->getImageIterations(),
            ];
            $image->setEphemeralImagineOptions($options);
        }, function (IImage $iframe) use ($size, $mode, &$animated) {
            $animated->layers()->add($iframe->thumbnail($size, $mode));
        }, function (IImage $iimage) {
            // nothing
        })) {
            // nothing
            return true;
        }
        $image->setImagineImage($image->getImagineImage()->thumbnail($size, $mode, $this->filter));
        return true;
    }

    protected function handlerZoom(ImageWithImagineInterface $image, $format)
    {
        if (preg_match('/^x([0-9]*\.?[0-9]+)$/', $format, $matches)) {
            $scale = floatval($matches[1]);
            $size = $image->getImagineImage()->getSize();
            $newSize = $size->scale($scale);
            $image->setImagineImage($image->getImagineImage()->resize($newSize)->crop(new \Imagine\Image\Point(
                max(0, ($newSize->getWidth() - $size->getWidth()) / 2),
                max(0, ($newSize->getHeight() - $size->getHeight()) / 2)
            ), $size));
            return true;
        }
        return false;
    }

    protected function handlerCropCanvas(ImageWithImagineInterface $image, $format)
    {
        $size = null;
        $width = $height = 0;
        $pp = false;
        if (preg_match('/^\=([0-9]+)([%]?)x([0-9]+)([%]?)$/', $format, $matches)) {
            $width = $matches[1];
            $height = $matches[3];
            if ($matches[2] == '%') {
                $width = $image->getImagineImage()->getSize()->getWidth() * $width / 100;
            }
            if ($matches[4] == '%') {
                $height = $image->getImagineImage()->getSize()->getHeight() * $height / 100;
            }
        } elseif (preg_match('/^\=([0-9]+)([%]?)$/', $format, $matches)) {
            $width = $height = $matches[1];
            if ($matches[2] == '%') {
                $width = $image->getImagineImage()->getSize()->getWidth() * $width / 100;
                $height = $image->getImagineImage()->getSize()->getHeight() * $height / 100;
            }
        }

        if ($width > 0 && $height > 0) {
            $size = new \Imagine\Image\Box($width, $height);
            // print_r([
            //     'ori_width' => $image->getImagineImage()->getSize()->getWidth(),
            //     'ori_height' => $image->getImagineImage()->getSize()->getHeight(),
            //     'width' => $width,
            //     'height' => $height,
            //     'x' => ($image->getImagineImage()->getSize()->getWidth() - $size->getWidth()) / 2,
            //     'y' => ($image->getImagineImage()->getSize()->getHeight() - $size->getHeight()) / 2,
            // ]); die();
            $image->setImagineImage($image->getImagineImage()->crop(new \Imagine\Image\Point(
                ($image->getImagineImage()->getSize()->getWidth() - $size->getWidth()) / 2,
                ($image->getImagineImage()->getSize()->getHeight() - $size->getHeight()) / 2
            ), $size));
            $image->deprecateBinaryContent();
            return true;
        }

        return false;
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