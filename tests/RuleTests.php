<?php
namespace ImageStack\Tests;

use ImageStack\ImagePath;
use ImageStack\ImageBackend\PathRule\PatternPathRule;

class RuleTests extends \PHPUnit_Framework_TestCase
{
    public function testPathRule()
    {
        $path = new ImagePath('foo/bar/123.jpg', 'stack');
        
        $rule = new PatternPathRule('|never|', true);
        $this->assertNull($rule->createPath($path));
        
        $rule = new PatternPathRule('|^foo/bar/|', true);
        $newPath = $rule->createPath($path);
        $this->assertNotNull($newPath);
        $this->assertEquals($path, $newPath);
        $this->assertEquals($path->getPrefix(), $newPath->getPrefix());
        $this->assertEquals($path->getPath(), $newPath->getPath());
        
        $rule = new PatternPathRule('|^foo/bar/|', false);
        $this->assertNull($rule->createPath($path));
        
        $rule = new PatternPathRule('|^(foo/bar/)(.*)$|', function ($matches) {
            return 'baz/' . $matches[2];
        });
        $newPath = $rule->createPath($path);
        $this->assertNotNull($newPath);
        $this->assertNotEquals($path, $newPath);
        $this->assertEquals($path->getPrefix(), $newPath->getPrefix());
        $this->assertEquals('baz/123.jpg', $newPath->getPath());
        
        $rule = new PatternPathRule('|^(foo/bar/)(.*)$|', function ($matches) {
            return new ImagePath('baz/' . $matches[2], 'other');
        });
        $newPath = $rule->createPath($path);
        $this->assertNotNull($newPath);
        $this->assertNotEquals($path, $newPath);
        $this->assertEquals('other', $newPath->getPrefix());
        $this->assertEquals('baz/123.jpg', $newPath->getPath());
        
        $rule = new PatternPathRule('|^(foo/bar/)(.*)$|', function ($matches, $path) {
            return $path;
        });
        $newPath = $rule->createPath($path);
        $this->assertNotNull($newPath);
        $this->assertEquals($path, $newPath);
        unset($newPath);
        
        $rule = new PatternPathRule('|^(foo/bar/)(.*)$|', ['baz/', 2]);
        $newPath = $rule->createPath($path);
        $this->assertNotNull($newPath);
        $this->assertNotEquals($path, $newPath);
        $this->assertEquals($path->getPrefix(), $newPath->getPrefix());
        $this->assertEquals('baz/123.jpg', $newPath->getPath());

        $rule = new PatternPathRule('|^(foo/bar/)(.*)$|', 'baz/${2}');
        $newPath = $rule->createPath($path);
        $this->assertNotNull($newPath);
        $this->assertNotEquals($path, $newPath);
        $this->assertEquals($path->getPrefix(), $newPath->getPrefix());
        $this->assertEquals('baz/123.jpg', $newPath->getPath());
    }
}