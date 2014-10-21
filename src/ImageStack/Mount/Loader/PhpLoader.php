<?php
namespace ImageStack\Mount\Loader;

use ImageStack\OptionableComponent;

class PhpLoader extends ArrayLoader {
	public function load($filename) {
	    if (!is_file($filename)) {
	        throw new \RuntimeException(sprintf("%s is no a file", $filename));
	    }
	    $app = $this->app;
	    $data = include $filename;
	    if (is_array($data)) {
	        // if the included file returns an array, we process it like a config
	    	parent::load($data);
	    }
	    else {
	    	//otherwise the php file should have set all it needed directly using the $app ref
	    }
	}
}