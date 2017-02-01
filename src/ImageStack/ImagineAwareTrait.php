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
     * @param ImagineInterface $imagine
     */
    public function setImagine(ImagineInterface $imagine = null)
    {
        $this->imagine = $imagine;
    }

    /**
     * @return \Imagine\Image\ImagineInterface
     */
    public function getImagine()
    {
        return $this->imagine;
    }
    
}
