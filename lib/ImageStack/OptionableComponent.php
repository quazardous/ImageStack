<?php
namespace ImageStack;

use Silex\Application as ApplicationBase;

abstract class OptionableComponent implements ApplicationAwareInterface {
	
	/**
	 * @var \Silex\Application
	 */
	protected $app;
	
	protected $options = array();
	
	public function __construct(array $options = array()) {
		$this->options = array_merge_recursive_distinct($this->options, $options);
	}
	
	public function setApp(ApplicationBase $app) {
		if ($this->app) return;
		$this->app = $app;
		foreach (get_object_vars($this) as $prop) {
			// recursive injection
			if ($prop instanceof ApplicationAwareInterface) {
				$prop->setApp($app);
			}
		}
	}
	
	public function getName() {
		$class = get_class($this);
		if (isset($this->options['name'])) {
			$class.="[{$this->options['name']}]";
		}
		return $class;
	}
	
}