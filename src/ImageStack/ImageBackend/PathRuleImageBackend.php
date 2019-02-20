<?php
namespace ImageStack\ImageBackend;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\Image;
use ImageStack\OptionnableTrait;
use ImageStack\ImageBackend\PathRule\PathRuleInterface;
use ImageStack\Api\Exception\ImageNotFoundException;
use ImageStack\Api\ImageInterface;

/**
 * Path rule image backend.
 * Manipulate path with rules before fetching from another backend with the manipulated path.
 * Path manipulation should not affect calling context.
 * 
 */
class PathRuleImageBackend implements ImageBackendInterface
{
    use OptionnableTrait;
    
    /**
     * @var ImageBackendInterface
     */
    protected $imageBackend;
    
    /**
     * Cache image backend constructor.
     * @param ImageBackendInterface $imageBackend
     * @param array $rules
     * @param array $options
     */    
    public function __construct(ImageBackendInterface $imageBackend, array $rules = [], $options = array())
    {
        $this->imageBackend = $imageBackend;
        foreach ($rules as $rule) {
            $this->addPathRule($rule);
        }
        $this->setOptions($options);
    }
    
    /**
     * @var PathRuleInterface[]
     */
    protected $rules = [];
    
    /**
     * Add path rule.
     * @param PathRuleInterface $rule
     */
    public function addPathRule(PathRuleInterface $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * {@inheritDoc}
     * @see \ImageStack\Api\ImageBackendInterface::fetchImage()
     * @return ImageInterface
     * @throws ImageNotFoundException if no match
     */
    public function fetchImage(ImagePathInterface $path)
    {
        foreach ($this->rules as $rule) {
            if ($newPath = $rule->createPath($path)) {
                return $this->imageBackend->fetchImage($newPath);
            }
        }
        throw new ImageNotFoundException(sprintf('Image not found: %s', $path->getPath()));
    }
    
}

