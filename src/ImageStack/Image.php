<?php
namespace ImageStack;

use ImageStack\Api\ImageInterface;
use ImageStack\Exception\ImageException;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface as ImagineImageInterface;

/**
 * Image implementation with imagine support.
 * 
 * Imagine image is used as underlaying image data.
 * We try to lazy manage binary content to minimize imagine processing
 *   when many image manipulators uses imagine image one after each other.
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
     * @var boolean
     */
    protected $binaryContentDirty;
    
    /**
     * @return boolean
     */
    public function getBinaryContentDirty()
    {
        return $this->binaryContentDirty;    
    }
    
    /**
     * Flag image binary content to be regenerated from imagine image.
     * @param boolean $dirty
     */
    public function setBinaryContentDirty($dirty = true)
    {
        $this->binaryContentDirty = $dirty;
    }

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
        $this->setBinaryContentDirty(false);
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\Api\ImageInterface::getBinaryContent()
     */
    public function getBinaryContent()
    {
        if ($this->getBinaryContentDirty()) {
            $mimeType = $this->getMimeType();
            $binaryContent = $this->getImagineImage()->get(
                    self::get_type_from_mime_type($mimeType),
                    $this->imagineOptions);
            $this->setBinaryContent($binaryContent, $mimeType);
        }
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
            $this->mimeType = $finfo->buffer($this->getBinaryContent());
            if (empty($this->mimeType)) {
                throw new ImageException('Cannot determine MIME type', ImageException::CANNOT_DETERMINE_MIME_TYPE);
            }
        }
        return $this->mimeType;
    }
    
    /**
     * @var ImagineInterface
     */
    protected $imagine = null;
    
    /**
     * @var array
     */
    protected $imagineOptions = [];
    
    /**
     * Set imagine interace.
     * @param ImagineInterface $imagine
     * @param array $imagineOptions
     */
    public function setImagine(ImagineInterface $imagine, array $imagineOptions = [])
    {
        $this->imagine = $imagine;
    }
    
    /**
     * Set imagine options for binary content generation.
     * @param array $imagineOptions
     */
    public function setImagineOptions(array $imagineOptions)
    {
        $this->imagineOptions = array_replace($this->imagineOptions, $imagineOptions);
    }
    
    /**
     * Get imagine interface.
     * @return ImagineInterface
     */
    public function getImagine()
    {
        return $this->imagine;
    }
    
    protected function assertImagine()
    {
        if (!$this->getImagine()) {
            throw new ImageException('Imagine not setup', ImageException::IMAGINE_NOT_SETUP);
        }
    }
    
	/**
	 * @var ImagineImageInterface
	 */
	protected $imagineImage;
	
	/**
	 * Return an iamgine image object from the image binary content.
	 * @throws ImageException
	 * @return ImagineImageInterface
	 */
    public function getImagineImage()
    {
        if (empty($this->imagineImage)) {
            $this->assertImagine();
            $binaryContent = $this->getBinaryContent();
            if (empty($binaryContent)) {
                throw new ImageException('Empty image', ImageException::EMPTY_IMAGE);
            }
            $this->imagineImage = $this->getImagine()->load($binaryContent);
        }
        return $this->imagineImage;
    }
    
    /**
     * Set an imagine image to replace the content.
     * @param ImagineImageInterface $imagineImage
     * @param string $mimeType
     * 
     * Changing the image OR the MIME type will flag the binary content as "dirty".
     * Calling getBinaryContent() will trigger an ImagineImageInterface::get().
     * 
     * Passing a NULL value to $imagineImage will immediately trigger the binary
     *   content generation (before null assignment). $mimeType will be ignored.
     * 
     * @see self::getBinaryContent()
     */
    public function setImagineImage(ImagineImageInterface $imagineImage = null, $mimeType = null)
    {
        if ($imagineImage) {
            if (!$mimeType) {
                $mimeType = $this->getMimeType();
            }
            if ($this->imagineImage !== $imagineImage || $mimeType != $this->getMimeType()) {
                // imagine image was changed OR MIME type was changed
                //  -> we flag binary content to be regerated from imagine image
                $this->imagineImage = $imagineImage;
                $this->mimeType = $mimeType;
                $this->setBinaryContentDirty();
            }
        } else {
            // assert binary content is clean
            $this->getBinaryContent();
            $this->imagineImage = null;
        }
    }
    
    /**
     * Get the image short type from the MIME type.
     * @param string $mimeType
     * @return string
     */
    public static function get_type_from_mime_type($mimeType) {
        $types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];
        if (isset($types[$mimeType])) return $types[$mimeType];
        throw new ImageException(sprintf('Unsupported MIME type: %s', $mimeType), ImageException::UNSUPPORTED_MIME_TYPE);
    }
}