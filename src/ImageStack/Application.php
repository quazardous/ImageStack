<?php
namespace ImageStack;

use Silex\Application as ApplicationBase;
use ImageStack\ApplicationAwareInterface;

class Application extends ApplicationBase {
    protected $aware = array();
    
    /**
     * Container reference injection.
     * @see \Pimple\Container::offsetGet()
     */
    public function offsetGet($id)
    {
        $value = parent::offsetGet($id);
        if (!isset($this->aware[$id])) {	
            $this->aware[$id] = true;
            if ($value instanceof ApplicationAwareInterface) {
            	$value->setApp($this);
            }
        }
        
        return $value;
    }
    
    public function log($component, $message, $level = 'info') {
        if (isset($this['logger']) && isset($this['monolog.logger.class']) && is_a($this['logger'], $this['monolog.logger.class'])) {
            $this['logger']->$level(sprintf("%s %s", $component, $message));
        }
    }
}