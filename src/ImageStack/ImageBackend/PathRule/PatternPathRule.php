<?php
namespace ImageStack\ImageBackend\PathRule;

use ImageStack\Api\ImagePathInterface;
use ImageStack\ImagePath;

/**
 * Pattern path rule.
 *
 */
class PatternPathRule implements PathRuleInterface
{

    /**
     * A preg_match() pattern.
     * @var string
     */
    protected $pattern;
    
    /**
     * An output.
     * @var string|array|callable
     */
    protected $output;
    
    /**
     * Pattern path rule constructor.
     * @param string $pattern to match the path
     * @param string|int[]|callable|boolean $format thumbnail format
     *  - string: an output format, ${N} (${0}, ${1}, ...) will be replaced by the preg_match() 3rd arg ($matches)
     *  - callable: the callbable wille be executed with $matches as 1st arg and original path as 2nd arg
     *  - array: the $matches items will be concatenated using int as idexes of $matches
     *  - true: a copy of path
     *  - false: null
     *  
     *  Output arg examples with ['...', 'lemon', 'orange'] as $matches:
     *  - "fruits/${1}/${2}": "fruits/lemon/orange"
     *  - function ($matches, $path) { return $matches[2] . '/' . $matches[1]; }: "orange/lemon"
     *  - [1, "/", 2]: "lemon/orange"
     */
    public function __construct($pattern, $output) {
        $this->pattern = $pattern;
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ImageStack\ImageBackend\PathRule\PathRuleInterface::createPath()
     * @return ImagePathInterface
     */
    public function createPath(ImagePathInterface $path)
    {
        $matches = null;
        if (!preg_match($this->pattern, $path->getPath(), $matches)) {
            return null;
        }
        if (false === $this->output) {
            return null;
        }
        
        if (true === $this->output) {
            return $path;
        }
        
        if (is_callable($this->output)) {
            $newPath = call_user_func($this->output, $matches, $path);
            if ($newPath instanceof ImagePathInterface) {
                return $newPath;
            }
            return new ImagePath($newPath, $path->getPrefix());
        }
        
        if (is_string($this->output)) {
            $placeholders = [];
            foreach ($matches as $i => $v) {
                $placeholders[sprintf('${%d}', $i)] = $v;
            }
            return new ImagePath(strtr($this->output, $placeholders), $path->getPrefix());
        }
        
        $output = (array) $this->output;
        
        $newPath = '';
		foreach ($output as $item) {
			if (is_integer($item)) {
				$newPath .= $matches[$item];
			}
			else {
				$newPath .= (string)$item;
			}
		}
        
		return new ImagePath($newPath, $path->getPrefix());
    }

}