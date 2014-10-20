<?php
namespace ImageStack\Cache;

interface CacheInterface {

	/**
	 * Get the data from cache
	 *
	 * @param string $cid
	 *
	 * @return mixed|null
	 *
	 */
	public function cacheGet($cid);
	
	/**
	 * Set data to cache
	 * 
	 * @param string $cid
	 * @param mixed $data if null remove
	 * 
	 * @return false
	 */
	public function cacheSet($cid, $data);
}