<?php
namespace ImageStack\Mount\Loader;

use ImageStack\OptionableComponent;

class YamlLoader extends OptionableComponent implements LoaderInterface {
	public function load($filename) {
		$data = include $filename;
		if (is_array($data)) {
			
		}
	}
}