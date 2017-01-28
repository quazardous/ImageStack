<?php
namespace ImageStack\Tests;

use ImageStack\ImagePath;

class ImagePathTests extends \PHPUnit_Framework_TestCase
{
    public function test_sanitize_path()
    {
        $this->assertEquals('/a/b/d', sanitize_path('///a/./b/c/../d'));
        $this->assertEquals('a/b/d', sanitize_path('./a/./b/c/../d'));
    }
    
    public function testImagePath()
    {
        $path = 'a/b/c/d.jpg';
        
        $imagePath = new ImagePath($path);
        $this->assertEquals($path, $imagePath->getPath());
        
        $prefix = 'stack';
        $imagePath = new ImagePath($path, $prefix);
        $this->assertEquals($path, $imagePath->getPath());
        $this->assertEquals($prefix, $imagePath->getPrefix());
    }
    
}