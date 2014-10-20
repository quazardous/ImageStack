<?php
namespace ImageStack\Mount\Loader;

use ImageStack\OptionableComponent;

class PhpLoader extends OptionableComponent implements LoaderInterface {
	public function load($filename) {
	    $app = $this->app;
	    $data = include $filename;
	    if (is_array($data)) {
	       foreach ($data as $key => &$value) {
	       	   $app[$key] =& $value; 
	       }
	    }
	}
}