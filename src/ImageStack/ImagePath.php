<?php
namespace ImageStack;

use ImageStack\Api\ImagePathInterface;

/**
 * Simple image path.
 *
 */
class ImagePath implements ImagePathInterface {

    protected $path;
    protected $prefix;
    
    /**
     * Image path constructor.
     * @param string $path
     * @param string $prefix
     */
    public function __construct($path, $prefix = null)
    {
        $this->path = $path;
        $this->prefix = $prefix;
    }
    
    /**
     * {@inheritdoc}
     *
     * @see \ImageStack\Api\ImagePathInterface::getPath()
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ImageStack\Api\ImagePathInterface::getPrefix()
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

}