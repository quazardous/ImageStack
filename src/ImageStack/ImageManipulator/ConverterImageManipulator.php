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

/**
 * Converter image manipulator.
 * Handles only ImageWithImagineInterface images. 
 *
 */
class ConverterImageManipulator implements ImageManipulatorInterface, ImagineAwareInterface {
    use OptionnableTrait;
    use ImagineAwareTrait;
    
    /**
     * Converter image manipulator constructor.
     * @param ImagineInterface $imagine
     * @param array $conversions associative array [ <current_mime> => <new_mime> ]
     * @param array $options
     *   - imagine_options : array of options to pass to imagine (jpeg_quality, png_compression_level, etc)
     */
	public function __construct(ImagineInterface $imagine, array $conversions = [], array $options = [])
	{
        $this->setImagine($imagine, $options['imagine_options'] ?? []);
        unset($options['imagine_options']);
        foreach ($conversions as $sourceMimeType => $destinationMimeType) {
            $this->addConversion($sourceMimeType, $destinationMimeType);
        }
        $this->setOptions($options);
	}
	
	/**
	 * @var string[string]
	 */
    protected $convertMap = [];
    
    /**
     * Add a new conversion map.
     * @param string $sourceMimeType
     * @param string $destinationMimeType
     */
    public function addConversion($sourceMimeType, $destinationMimeType)
    {
        if ($sourceMimeType == $destinationMimeType) return;
        $this->convertMap[$sourceMimeType] = $destinationMimeType;
    }
    
	/**
	 * {@inheritDoc}
	 * @see \ImageStack\Api\ImageManipulatorInterface::manipulateImage()
	 */
    public function manipulateImage(ImageInterface $image, ImagePathInterface $path)
    {
        return $this->_manipulateImage($image, $path);
    }
    
    /**
     * Enforce use of ImageWithImagineInterface
     * @param ImageWithImagineInterface $image
     * @param ImagePathInterface $path
     */
    protected function _manipulateImage(ImageWithImagineInterface $image, ImagePathInterface $path)
    {
        if (isset($this->convertMap[$image->getMimeType()])) {
            if (!$image->getImagine()) {
                $image->setImagine($this->getImagine(), $this->getImagineOptions());
            }
            // trigger imagine image creation
            $image->getImagineImage();
            $image->setMimeType($this->convertMap[$image->getMimeType()]);
        }
    }
}