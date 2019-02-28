<?php
namespace ImageStack;

use Imagine\Image\ImagineInterface;

/**
 * Imagine aware trait.
 *
 */
trait ImagineAwareTrait
{
    /**
     * @var ImagineInterface
     */
    protected $imagine = null;

    /**
     * @var array
     */
    protected $imagineOptions = [];
    
    /**
     * @param ImagineInterface $imagine
     */
    public function setImagine(ImagineInterface $imagine = null, array $imagineOptions = [])
    {
        $this->imagine = $imagine;
        $this->imagineOptions = $imagineOptions;
    }

    /**
     * @return \Imagine\Image\ImagineInterface
     */
    public function getImagine()
    {
        return $this->imagine;
    }

    /**
     * @return array
     */
    public function getImagineOptions()
    {
        return $this->imagineOptions;
    }
    
}
