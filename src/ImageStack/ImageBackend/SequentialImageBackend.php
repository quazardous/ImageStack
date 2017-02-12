<?php
namespace ImageStack\ImageBackend;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\OptionnableTrait;
use ImageStack\Api\Exception\ImageNotFoundException;

/**
 * Sequential image backend.
 * Sequentialy fetches images from a queue of image backends and returns the first match.
 */
class SequentialImageBackend implements ImageBackendInterface
{
    use OptionnableTrait;

    /**
     * Sequential image backend constructor.
     * 
     * @param ImageBackendInterface[] $imageBackends a queue of image backends
     * @param array $options            
     */
    public function __construct(array $imageBackends = [], $options = array())
    {
        foreach ($imageBackends as $imageBackend) {
            $this->addImageBackend($imageBackend);
        }
        $this->setOptions($options);
    }

    public function fetchImage(ImagePathInterface $path)
    {
        foreach ($this->imageBadkends as $imageBackend) {
            try {
                if ($image = $imageBackend->fetchImage($path)) {
                    return $image;
                }
            } catch (ImageNotFoundException $e) {
                //nothing
            }
        }
        throw new ImageNotFoundException(sprintf('Image Not Found : %s', $path->getPath()));
    }
    
    /**
     * @var ImageBackendInterface[]
     */
    protected $imageBadkends = [];
    
    /**
     * Append an image backend.
     * @param ImageBackendInterface $imageBackend
     * @throws \InvalidArgumentException
     */
    public function addImageBackend(ImageBackendInterface $imageBackend)
    {
        if ($imageBackend === $this) {
            throw new \InvalidArgumentException('Recursion error');
        }
        $this->imageBadkends[] = $imageBackend;
    }
}

