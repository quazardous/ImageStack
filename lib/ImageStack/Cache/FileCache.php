<?php
namespace ImageStack\Cache;

use ImageStack\OptionableComponent;

class FileCache extends OptionableComponent implements CacheInterface {

	public function __construct($options = array()) {
		if (!is_array($options)) {
			$options = array('cache_dir' => $options);
		}
		
		parent::__construct($options);
		
		if (!isset($this->options['cache_dir'])) {
			throw new \InvalidArgumentException("missing 'cache_dir' option");
		}
		$this->options['cache_dir'] = rtrim($this->options['cache_dir'], '/');
	}
	
	public function cacheGet($cid) {
		// pas de sortie d'erreur !
		$res = @file_get_contents($filename = $this->getFilename($cid));
		if ($res !== false) {
			$this->app['logger']->info($this->getName()." $filename >");
		}
		return ($res===false ? null : $res);
	}
	
	public function cacheSet($cid, $data) {
		$filename = $this->getFilename($cid);
		$dirname = dirname($filename);
		if (!is_dir($dirname)) {
			@mkdir($dirname, 0755, true);
		}
		if ($data === null) {
		  @unlink($filename);
		  return null;
		}
	  $res = file_put_contents($filename, $data);
		if ($res !== false) {
			$this->app['logger']->info($this->getName()." $filename <");
		}
		return $res;
	}
	
	protected function getFilename($cid) {
		return $this->options['cache_dir'] . '/' . ltrim($cid, '/');
	}
}