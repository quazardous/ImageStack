<?php
namespace ImageStack\ImageBackend;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\Api\Exception\ImageBackendException as ApiImageBackendException;
use ImageStack\ImageBackend\Exception\ImageBackendException;
use ImageStack\OptionnableTrait;

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
            } catch (ApiImageBackendException $e) {
                if ($e->getCode() !== ApiImageBackendException::IMAGE_NOT_FOUND) {
                    throw $e;
                }
            }
        }
        throw new ImageBackendException(sprintf('Image Not Found : %s', $path->getPath()), ImageBackendException::IMAGE_NOT_FOUND);
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

