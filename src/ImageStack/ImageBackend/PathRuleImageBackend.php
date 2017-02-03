<?php
namespace ImageStack\ImageBackend;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\Image;
use ImageStack\OptionnableTrait;
use ImageStack\ImageBacken\PathRule\PathRuleInterface;

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
     * @param Cache $cache
     * @param array $options
     * Options:
     *   - cache_prefix : prefix for the cache (default: stack prefix)
     *   - cache_lifetime : cache lifetime (default: 0 is forever)
     *   - cache_id_sanitizer : callback to sanitize the cache ID (default: none)
     */    
    public function __construct(ImageBackendInterface $imageBackend, array $rules = [], $options = array())
    {
        $this->imageBackend = $imageBackend;
        foreach ($rules as $rule) {
            $this->addPathRule($rule);
        }
        $this->setOptions($options);
    }
    
    protected $rules = [];
    public function addPathRule(PathRuleInterface $rule)
    {
        $this->rules[] = $rule;
    }

    public function fetchImage(ImagePathInterface $path)
    {
        $cid = $this->getCacheId($path);
        if (false !== ($binaryContent = $this->cache->fetch($cid))) {
            return new Image($binaryContent);
        }
        $image = $this->imageBackend->fetchImage($path);
        $this->cache->save($cid, $image->getBinaryContent(), $this->getOption('cache_lifetime', 0));
        return $image;
    }
    
}

