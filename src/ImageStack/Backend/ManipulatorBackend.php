<?php
namespace ImageStack\Backend;

use ImageStack\Image;
use ImageStack\Manipulator\ManipulatorInterface;
use ImageStack\Cache\CacheInterface;
use ImageStack\Backend\BackendInterface;
use ImageStack\OptionableComponent;

/**
 * Utilise un backend pour récupérer une image source et crée des versions "manipulées" (ex: thumbnails).
 * Utilise un cache pour garder l'image de source.
 */
class ManipulatorBackend extends OptionableComponent implements BackendInterface {
	
	/**
	 * 
	 * @var \ImageStack\Manipulator\ManipulatorInterface
	 */
	protected $manipulator;
	
	/**
	 *
	 * @var \ImageStack\Cache\CacheInterface
	 */
	protected $cache;
	
	/**
	 * Backend source
	 * @var \ImageStack\Backend\BackendInterface
	 */
	protected $backend;
	
	/**
	 * @param array $options
	 * @throws \BadMethodCallException
	 */
	public function __construct(BackendInterface $backend, ManipulatorInterface $manipulator, CacheInterface $cache, $options = array()) {
		
	  $this->backend = $backend;
		$this->manipulator = $manipulator;
		$this->cache = $cache;
		parent::__construct($options);

		if (!isset($this->options['patterns'])) {
			throw new \InvalidArgumentException("missing 'patterns' option");
		}
	}

	
	/**
	 * (non-PHPdoc)
	 * @see \ImageStack\Backend\BackendInterface::getImage()
	 */
	public function getImage($path) {
		
	  // on determine le chemin de l'image source
		$src = false;
		$cid = false;
		foreach ($this->options['patterns'] as $pattern => $assemble) {
			if (preg_match($pattern, $path, $matches)) {
				
			    // chemin source
			    if (!isset($assemble['path'])) {
			        $assemble['path'] = $assemble;
			    }
			    $assemble['path'] = (array)$assemble['path'];
			    // chemin cache
			    if (!isset($assemble['cache'])) {
			        $assemble['cache'] = $assemble['path'];
			    }
			    $assemble['cache'] = (array)$assemble['cache'];
			    
			    $src = '';
				// on assemble les matches..
				foreach ($assemble['path'] as $item) {
					if (is_integer($item)) {
						$src .= $matches[$item];
					}
					else {
						$src .= $item;
					}
				}
				
				$cid = '';
				// on assemble les matches..
				foreach ($assemble['cache'] as $item) {
				    if (is_integer($item)) {
				        $cid .= $matches[$item];
				    }
				    else {
				        $cid .= $item;
				    }
				}
				
				break;
			}
		}
		if (!$src) return false;
		
		$src = ltrim($src, '/');
		$cid = ltrim($cid, '/');
		
		// on récupère l'image source
		
		// on vérifie en cache avec le chemin trouvé
		if (!($data = $this->cacheGet($cid))) {
		  // on demande au backend source
		  $image = $this->backend->getImage($src);
		  if (!$image) {
		    return false;
		  }
		  $data = $image->getData();
		  $this->cacheSet($cid, $data);
		}
		
		$type = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if ($type == 'jpg') {
			$type = 'jpeg';
		}
		
		try {
		  $image = new Image($this->app['imagine'], $type, $data);
		}
		catch (\Exception $e) {
		  // fix
		  $this->app->log($this->getName(), "fix cache $path");
		  $this->cacheSet($src, null);
		  return false;
		}
		
		// on crée une version manipulée
		if (!$this->manipulator->manipulate($image, $path)) return false;
		
		return $image;
	}
	
	protected function cacheSet($src, $data) {
		if ($this->cache) return $this->cache->cacheSet($src, $data);
		return false;
	}
	
	protected function cacheGet($src) {
		if ($this->cache) return $this->cache->cacheGet($src);
		return false;
	}
	
}