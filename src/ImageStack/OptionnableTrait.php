<?php
namespace ImageStack;

trait OptionnableTrait
{
    /**
     * @var array
     */
    protected $options = [];
    
    /**
     * Set all options.
     * @param array $options
     */
    public function setOptions(array $options, $merge = true)
    {
        if ($merge) {
            $this->options = array_replace($this->options, $options);
        } else {
            $this->options = $options;
        }
    }
    
    /**
     * Get all options.
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Set option value.
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }
    
    /**
     * Get option value or default or throws exception.
     * @param string $name
     * @param mixed|callable|\Exception $default
     * @throws \Exception
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        if (!isset($this->options[$name])) {
            if (is_callable($default)) {
                $default = call_user_func($default);
            } if ($default instanceof \Exception) {
                throw $default;
            }
            return $default;
        }
        return $this->options[$name];
    }
    
}
