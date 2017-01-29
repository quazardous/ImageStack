<?php
namespace ImageStack\Tests;

use ImageStack\Image;
use ImageStack\ImageOptimizer\JpegtranImageOptimizer;
use ImageStack\ImageOptimizer\PngcrushImageOptimizer;

class ImageOptimizerTests extends \PHPUnit_Framework_TestCase
{
    
    public function testJpegtranOptimizer()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        
        $optimizer = new JpegtranImageOptimizer();
        
        $optimizer->optimizeImage($image);
        
        $this->assertStringEqualsFile(__DIR__ . '/resources/optimizer/cat1_jpegtran.jpg', $image->getBinaryContent());
    }
    
    public function testPngcrushOptimizer()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat2_original.png'));
        
        $optimizer = new PngcrushImageOptimizer();
        
        $optimizer->optimizeImage($image);
        
        $this->assertStringEqualsFile(__DIR__ . '/resources/optimizer/cat2_pngcrush.png', $image->getBinaryContent());
    }
}