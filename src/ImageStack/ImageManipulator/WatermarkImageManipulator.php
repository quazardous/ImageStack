<?php
namespace ImageStack\ImageManipulator;

use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\Api\ImageManipulatorInterface;
use ImageStack\Image;
use ImageStack\OptionnableTrait;
use ImageStack\ImageWithImagineInterface;
use Imagine\Image\ImagineInterface;
use ImageStack\ImagineAwareTrait;
use ImageStack\ImagineAwareInterface;
use Imagine\Image\Point;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Imagick\Image as IImage;

/**
 * Watermark image manipulator.
 * Add a watermak to the image.
 * Handles only ImageWithImagineInterface images. 
 *
 */
class WatermarkImageManipulator implements ImageManipulatorInterface, ImagineAwareInterface {
    use OptionnableTrait;
    use ImagineAwareTrait;
    use AnimatedGifAwareImageManipulatorTrait;
    
    const ANCHOR_LEFT   = 0x01;
    const ANCHOR_CENTER = 0x02;
    const ANCHOR_RIGHT  = 0x04;
    const ANCHOR_TOP    = 0x10;
    const ANCHOR_MIDDLE = 0x20;
    const ANCHOR_BOTTOM = 0x40;
    
    const REPEAT_NONE = 0x00;
    const REPEAT_X    = 0x01;
    const REPEAT_Y    = 0x02;
    const REPEAT_ALL  = self::REPEAT_X|self::REPEAT_Y;
    
    const REDUCE_NONE     = 0x00;
    const REDUCE_INSET    = 0x01;
    const REDUCE_OUTBOUND = 0x02;
    
    /**
     * Watermark image manipulator constructor.
     * @param ImagineInterface $imagine
     * @param string $watermark watermark filename should be a png with transparency
     * @param array $options
     *   - anchor: bitwise addition of ANCHOR_XX constants (default: ANCHOR_MIDDLE|ANCHOR_CENTER)
     *   - repeat: bitwise addition of REPEAT_XX constants (default: REPEAT_NONE)
     *   - reduce: REDUCE_XX constants (default: REDUCE_NONE), the watermark will be reduced if image is smaller
     *   - imagine_options : array of options to pass to imagine (jpeg_quality, png_compression_level, etc)
     */
    public function __construct(ImagineInterface $imagine, $watermark, array $options = [])
    {
        $this->setImagine($imagine, $options['imagine_options'] ?? []);
        unset($options['imagine_options']);
        $this->watermark = $watermark;
        $this->setOptions($options);
    }
    
    /**
     * @var string
     */
    protected $watermark;
    
    /**
     * {@inheritDoc}
     * @see \ImageStack\Api\ImageManipulatorInterface::manipulateImage()
     */
    public function manipulateImage(ImageInterface $image, ImagePathInterface $path)
    {
        return $this->_manipulateImage($image, $path);
    }
    
    protected function isBit($value, $mask)
    {
        return ($value & $mask) === $mask;
    }
    
    /**
     * Enforce use of ImageWithImagineInterface
     * @param ImageWithImagineInterface $image
     * @param ImagePathInterface $path
     */
    protected function _manipulateImage(ImageWithImagineInterface $image, ImagePathInterface $path)
    {
        if (!$image->getImagine()) {
            $image->setImagine($this->getImagine(), $this->getImagineOptions());
        }
        
        $reduce = $this->getOption('reduce', static::REDUCE_NONE);
        $anchor = $this->getOption('anchor', static::ANCHOR_CENTER | static::ANCHOR_MIDDLE);
        $repeat = $this->getOption('repeat', static::REPEAT_NONE);
        
        $iImage = $image->getImagineImage();
        
        $watermark = $this->getWatermarkImagineImage($iImage);
        
        if ($iImage->getSize()->getWidth() < $watermark->getSize()->getWidth()
            || $iImage->getSize()->getHeight() < $watermark->getSize()->getHeight()) {
            // watermark is bigger than image
            // we need to reduce/crop
            
            $width = min($iImage->getSize()->getWidth(), $watermark->getSize()->getWidth());
            $height = min($iImage->getSize()->getHeight(), $watermark->getSize()->getHeight());

            if ($this->isBit($reduce, static::REDUCE_INSET)) {
                $watermark = $watermark->thumbnail($iImage->getSize(), \Imagine\Image\ImageInterface::THUMBNAIL_INSET);
            } elseif ($this->isBit($reduce, static::REDUCE_OUTBOUND)) {
                $watermark = $watermark->thumbnail($iImage->getSize(), \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
            } else {
                // we crop the watermark
                
                if ($this->isBit($anchor, static::ANCHOR_LEFT)) {
                    $x = 0;
                } elseif ($this->isBit($anchor, static::ANCHOR_RIGHT)) {
                    $x = $watermark->getSize()->getWidth() - $width;
                } else /*if ($this->isBit($anchor, static::ANCHOR_CENTER))*/ {
                    $x = intval(round(($watermark->getSize()->getWidth() - $width) / 2));
                } 
                
                if ($this->isBit($anchor, static::ANCHOR_TOP)) {
                    $y = 0;
                } elseif ($this->isBit($anchor, static::ANCHOR_BOTTOM)) {
                    $y = $watermark->getSize()->getHeight() - $height;
                } else /*if ($this->isBit($anchor, static::ANCHOR_MIDDLE))*/ {
                    $y = intval(round(($watermark->getSize()->getHeight() - $height) / 2));
                } 
                $watermark = $watermark->copy()->crop(new Point($x, $y), new Box($width, $height));
            }
        }

        // from here the watermark is always smaller than the image
        
        $repeatX = $iImage->getSize()->getWidth() > $watermark->getSize()->getWidth() && $this->isBit($repeat, static::REPEAT_X);
        $repeatY = $iImage->getSize()->getHeight() > $watermark->getSize()->getHeight() && $this->isBit($repeat, static::REPEAT_Y);
        
        $cols = 1;
        if ($repeatX) {
            $cols = intval(floor($iImage->getSize()->getWidth() / $watermark->getSize()->getWidth()));
        }
        $innerWatermarkGridWidth = $watermark->getSize()->getWidth() * $cols;
        
        $rows = 1;
        if ($repeatY) {
            $rows = intval(floor($iImage->getSize()->getHeight() / $watermark->getSize()->getHeight()));
        }
        $innerWatermarkGridHeight = $watermark->getSize()->getHeight() * $rows;
        
        // first complete watermark start
        if ($this->isBit($anchor, static::ANCHOR_LEFT)) {
            $deltaX = 0;
        } elseif ($this->isBit($anchor, static::ANCHOR_RIGHT)) {
            $deltaX = $iImage->getSize()->getWidth() - $innerWatermarkGridWidth;
        } else /*if ($this->isBit($anchor, static::ANCHOR_CENTER))*/ {
            $deltaX = intval(round(($iImage->getSize()->getWidth() - $innerWatermarkGridWidth) / 2));
        }
        
        if ($this->isBit($anchor, static::ANCHOR_TOP)) {
            $deltaY = 0;
        } elseif ($this->isBit($anchor, static::ANCHOR_BOTTOM)) {
            $deltaY = $iImage->getSize()->getHeight() - $innerWatermarkGridHeight;
        } else /*if ($this->isBit($anchor, static::ANCHOR_MIDDLE))*/ {
            $deltaY = intval(round(($iImage->getSize()->getHeight() - $innerWatermarkGridHeight) / 2));
        }
        
        if ($repeatX || $repeatY) {
            // we need to create a repeated watermark buffer
            // we will create a buffer made of plain repeated watermark tiles surrounding the image and crop it after
            if ($repeatX) {
                if ($deltaX > 0) {
                    ++$cols;
                }
                ++$cols;
            }
            if ($repeatY) {
                if ($deltaY > 0) {
                    ++$rows;
                }
                ++$rows;
            }
            
            $palette = new RGB();
            $buffer = $this->getImagine()->create(new Box($cols * $watermark->getSize()->getWidth(), $rows * $watermark->getSize()->getHeight()), $palette->color([255, 255, 255], 0));
            
            for ($i = 0; $i < $cols; ++$i) {
                for ($j = 0; $j < $rows; ++$j) {
                    $buffer->paste($watermark, new Point($i * $watermark->getSize()->getWidth(), $j * $watermark->getSize()->getHeight()));
                }
            }
            
            if ($repeatX) {
                $width = $iImage->getSize()->getWidth();
                $cropX = (($watermark->getSize()->getWidth() - $deltaX) % $watermark->getSize()->getWidth());
            } else {
                $width = $watermark->getSize()->getWidth();
                $cropX = 0;
            }
            if ($repeatY) {
                $height = $iImage->getSize()->getHeight();
                $cropY = (($watermark->getSize()->getHeight() - $deltaY) % $watermark->getSize()->getHeight());
            } else {
                $height = $watermark->getSize()->getHeight();
                $cropY = 0;
            }
            
            $watermark = $buffer->crop(new Point($cropX, $cropY), new Box($width, $height));
            
            if ($repeatX) $deltaX = 0;
            if ($repeatY) $deltaY = 0;
        }
        
        $deltaP = new Point($deltaX, $deltaY);
        
        if ($this->handleAnimatedGif($image, function (IImage $iimage) use ($image) {
            // we take first frame conf
            /** @var IImage $iframe */
            $iframe = $iimage->layers()[0];
            $options = [
                'flatten' => false,
                'animated' => true,
                'animated.delay' => $iframe->getImagick()->getImageDelay() * 10,
                'animated.loop' => $iframe->getImagick()->getImageIterations(),
            ];
            // put those options for next binary dump only
            $image->setEphemeralImagineOptions($options);
            
        }, function (IImage $iframe) use ($watermark, $deltaP) {
            $iframe->paste($watermark, $deltaP);
        }, function (IImage $iimage) {
            // nothing
        })) {
            
            $image->deprecateBinaryContent();
            return;
        }
        $iImage->paste($watermark, $deltaP);
        $image->deprecateBinaryContent();
    }
    
    /**
     * @var \Imagine\Image\ImageInterface
     */
    protected $watermarkImagineImage;
    
    /**
     * @return \Imagine\Image\ImageInterface
     */
    protected function getWatermarkImagineImage(\Imagine\Image\ImageInterface $iImage)
    {
        if (empty($this->watermarkImagineImage)) {
            if ($this->watermark instanceof \Imagine\Image\ImageInterface) {
                $this->watermarkImagineImage = $this->watermark;
            } elseif (is_callable($this->watermark)) {
                $this->watermarkImagineImage = call_user_func($this->watermark, $iImage, $this->getImagine());
            } else {
                $this->watermarkImagineImage = $this->getImagine()->open($this->watermark);
            }
        }
        return $this->watermarkImagineImage;
    }
}