<?php
namespace ImageStack\Tests;

use ImageStack\Image;
use ImageStack\ImageOptimizer\JpegtranImageOptimizer;
use ImageStack\ImageOptimizer\PngcrushImageOptimizer;
use ImageStack\ImageOptimizer\GifsicleImageOptimizer;

class ImageOptimizerTests extends \PHPUnit_Framework_TestCase
{
    
    public function testJpegtranOptimizer()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        
        $optimizer = new JpegtranImageOptimizer();
        
        $optimizer->optimizeImage($image);
        
        $this->assertStringEqualsFile(__DIR__ . '/resources/optimizer/cat1_jpegtran.jpg', $image->getBinaryContent());
    }
    
    /**
     * @expectedException ImageStack\ImageOptimizer\Exception\ImageOptimizerException
     * @expectedExceptionCode ImageStack\ImageOptimizer\Exception\ImageOptimizerException::UNSUPPORTED_MIME_TYPE
     */
    public function testPngcrushOptimizerUnsupportedMimeType()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        
        $optimizer = new PngcrushImageOptimizer();
        
        $optimizer->optimizeImage($image);
    }
    
    public function testPngcrushOptimizer()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat2_original.png'));
        
        $optimizer = new PngcrushImageOptimizer();
        
        $optimizer->optimizeImage($image);
        
        $this->assertStringEqualsFile(__DIR__ . '/resources/optimizer/cat2_pngcrush.png', $image->getBinaryContent());
    }

    public function testGifsicleOptimizer()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/eclipse256.gif'));
        
        $optimizer = new GifsicleImageOptimizer();
        
        $optimizer->optimizeImage($image);
        
        $this->assertStringEqualsFile(__DIR__ . '/resources/optimizer/eclipse256.gif', $image->getBinaryContent());
    }

    public function testGifsicleOptimizerAnim()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/animated.gif'));
        
        $optimizer = new GifsicleImageOptimizer();
        
        $optimizer->optimizeImage($image);
        
        $this->assertStringEqualsFile(__DIR__ . '/resources/optimizer/animated.gif', $image->getBinaryContent());
    }
}