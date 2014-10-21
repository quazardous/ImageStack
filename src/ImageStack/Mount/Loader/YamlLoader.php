<?php
namespace ImageStack\Mount\Loader;

use ImageStack\OptionableComponent;
use Symfony\Component\Yaml\Yaml;

class YamlLoader extends ArrayLoader {
	public function load($filename) {
	    $this->app->log($this->getName(), "load $filename");
	    
	    $yaml = Yaml::parse($filename);
	    
	    return parent::load($yaml);
	}
}