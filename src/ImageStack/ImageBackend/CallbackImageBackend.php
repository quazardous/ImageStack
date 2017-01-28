<?php
namespace ImageStack\ImageBackend;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\OptionnableTrait;
use ImageStack\Api\ImageInterface;
use ImageStack\ImageBackend\Exception\ImageBackendException;

/**
 * Callback image backend.
 * Use a callback to fetch images.
 */
class CallbackImageBackend implements ImageBackendInterface
{
    use OptionnableTrait;

    /**
     * @var callable
     */
    protected $callback;
    
    /**
     * Sequential image backend constructor.
     * 
     * @param callable $callback a callback
     *   Signature: function (ImagePathInterface $path) return ImageInterface
     *   
     * @param array $options            
     */
    public function __construct(callable $callback, $options = array())
    {
        $this->callback = $callback;
        $this->setOptions($options);
    }

    public function fetchImage(ImagePathInterface $path)
    {
        $image = call_user_func($this->callback, $path);
        if (!($image instanceof ImageInterface)) {
            throw new ImageBackendException(sprintf("Cannot read file : %s", $path->getPath()), ImageBackendException::CANNOT_READ_FILE);
        }
        return $image;
    }
    
}

