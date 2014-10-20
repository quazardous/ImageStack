<?php
namespace ImageStack\Cache;

use ImageStack\OptionableComponent;

/**
 * No cache.
 *
 */
class NullCache extends OptionableComponent implements CacheInterface {

	public function __construct() {
		parent::__construct(array());
	}
	
	public function cacheGet($cid) {
		return false;
	}
	
	public function cacheSet($cid, $data) {
		return false;
	}
}