<?php
namespace ImageStack;

use Imagine\Image\ImagineInterface;

/**
 * Imagine aware interface.
 *
 */
interface ImagineAwareInterface
{
    /**
     * Set imagine interace.
     * @param ImagineInterface $imagine
     */
    public function setImagine(ImagineInterface $imagine = null, array $imagineOptions = []);
    
    /**
     * Get imagine interface.
     * @return ImagineInterface
     */
    public function getImagine();
    
    
    /**
     * @return array
     */
    public function getImagineOptions();
}
