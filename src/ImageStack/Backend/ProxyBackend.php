<?php
namespace ImageStack\Backend;

use ImageStack\Image;
use ImageStack\OptionableComponent;

/**
 * Backend proxy.
 * Récupère l'image qui correspond au chemin en HTTP.
 *
 */
class ProxyBackend extends OptionableComponent implements BackendInterface {
	
	/**
	 * @param array $options
	 * @throws \BadMethodCallException
	 */
	public function __construct($options = array()) {
		
		if (!is_array($options)) {
			$options = array('backend_url' => $options);
		}
		
		$options += array(
		    'curl_connecttimeout' => 2,
		    'curl_timeout' => 10,
		);
		
		parent::__construct($options);
		
		if (!isset($this->options['backend_url'])) {
			throw new \InvalidArgumentException("missing 'backend_url' option");
		}
		$this->options['backend_url'] = rtrim($this->options['backend_url'], '/');
	  
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ImageStack\Backend\BackendInterface::getImage()
	 */
	public function getImage($path) {
		$url = $this->options['backend_url'] . '/' . ltrim($path, '/');
		if (!($data = $this->download($url))) return false;
		if (!$this->checkImage($data)) return false;
		$type = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if ($type == 'jpg') {
			$type = 'jpeg';
		}
		
		try {
		  $image = new Image($this->app['imagine'], $type, $data);
		}
		catch (\Exception $e) {
		  return false;
		}
		
		return $image;
	}
	
	protected function checkImage($data) {
	  try {
	    $this->app['imagine']->load($data);
	  }
	  catch (\Exception $e) {
	    return false;
	  }
	  return true;
	}
	
	protected function download($url) {
		$res = $this->httpRequest($url);
		if ($res['status'] == '404') return false;
		if ($res['status'] != '200' || strlen($res['data']) == 0) {
			throw new \RuntimeException("Error : $url invalid");
		}
		$this->app['logger']->info($this->getName()." $url >");
		return $res['data'];
	}
	
	
	protected function httpRequest($url) {
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
	
		if (isset($this->options['curl_proxy'])) {
			curl_setopt($c, CURLOPT_PROXY, $this->options['curl_proxy']);
			if (isset($this->options['curl_proxy_userpwd'])) {
				curl_setopt($c, CURLOPT_PROXYUSERPWD, $this->options['curl_proxy_userpwd']);
			}
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
		}
	
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT , $this->options['curl_connecttimeout']);

	
		curl_setopt($c, CURLOPT_TIMEOUT, $this->options['curl_timeout']);
	
		curl_setopt($c, CURLOPT_ENCODING, ''); // allow gzip/deflate if available
		curl_setopt ($c, CURLOPT_RETURNTRANSFER, 1);
		
		/*
		if (isset($this->options['backend_host'])) {
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $this->options['backend_host']));
		}*/
		
		$ret = curl_exec ($c);
		$errno = curl_errno($c);
		if ($errno == 22 || $errno == 0) {
		  $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		}
		else {
		  $http_status = 1000;
		}
		curl_close ($c);
	
		return array('data' => $ret, 'status' => $http_status);
	}
	
}