<?php
namespace ImageStack\ImageBackend;

use ImageStack\Api\ImageBackendInterface;
use ImageStack\Api\ImagePathInterface;
use ImageStack\OptionnableTrait;
use Doctrine\Common\Cache\Cache;
use ImageStack\Image;

/**
 * Cache image backend.
 * Use a cache on top of another image backend.
 */
class CacheImageBackend implements ImageBackendInterface
{
    use OptionnableTrait;

    /**
     * @var ImageBackendInterface
     */
    protected $imageBackend;
    
    /**
     * @var Cache
     */
    protected $cache;
    
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
    public function __construct(ImageBackendInterface $imageBackend, Cache $cache, $options = array())
    {
        $this->imageBackend = $imageBackend;
        $this->cache = $cache;
        $this->setOptions($options);
    }

    /**
     * Get the cache ID from the path and can do sanitize stuff.
     * @param ImagePathInterface $path
     * @return string
     */
    protected function getCacheId(ImagePathInterface $path) {
        if ($cid = $this->getOption('cache_prefix')) {
            $cid .= DIRECTORY_SEPARATOR;
        }
        $cid .= $path->getPath();
        if ($sanitizer = $this->getOption('cache_id_sanitizer')) {
            $cid = call_user_func($sanitizer, $cid);
        }
        return $cid;
    }
    
    public function fetchImage(ImagePathInterface $path)
    {
        $cid = $this->getCacheId($path);
        if (false !== ($binaryContent = $this->cache->fetch($cid))) {
            return new Image($binaryContent);
        }
        $image = $this->imageBackend->fetchImage($cid);
        $this->cache->save($cid, $image->getBinaryContent(), $this->getOption('cache_lifetime', 0));
        return $image;
    }
    
}

