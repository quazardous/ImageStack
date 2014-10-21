<?php
namespace ImageStack\Mount\Loader;

use ImageStack\OptionableComponent;

class ArrayLoader extends OptionableComponent implements LoaderInterface {
    public function load($array) {
        if (!is_array($array)) {
        	throw new \RuntimeException('Not an array');
        }
        
        foreach ($array as $tag => $definition) {
        	if (preg_match('/(mount|storage)\.([a-z0-9_]+)/i', $tag, $matches)) {
        	    $type = $matches[1];
        	    $id = $matches[2];
        		switch ($type) {
        			case 'mount':
        			    $this->loadMount($id, $definition);
        			    break;
        			case 'storage':
        			    $this->loadStorage($id, $definition);
        			    break;
        		}
        	}
        	else {
        	    throw new \RuntimeException(sprintf("%s : bad tag", $tag));
        	}
        }
    }
    
    protected function mountFactory($id, array $definition) {

        if (empty($definition['point'])) {
            $definition['point'] = $id;
        }
    	
    	if (empty($definition['type'])) {
    	    $definition['type'] = 'default';
    	}
    	
    	if (empty($definition['class'])) {
    	    $definition['class'] = 'ImageStack\\Mount\\' . \strtocamelcase($definition['type'], true) . 'Mount';
    	}
    	
    	if (empty($definition['storage'])) {
    	    $definition['storage'] = 'default';
    	}
    	
    	print_r($definition);
    }
    
    protected function storageFactory($id, array $definition) {

    }
}