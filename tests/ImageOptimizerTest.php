<?php
namespace ImageStack\Tests;

use ImageStack\Image;
use ImageStack\StorageBackend\ImageOptimizer\JpegtranImageOptimizer;
use ImageStack\StorageBackend\ImageOptimizer\PngcrushImageOptimizer;

class ImageOptimizerTest extends \PHPUnit_Framework_TestCase
{
    
    public function testJpegtranOptimizer()
    {
        $binaryContent = file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg');
        $image = new Image($binaryContent);
        
        $optimizer = new JpegtranImageOptimizer();
        
        $optimizer->optimizeImage($image);
        
        $this->assertStringEqualsFile(__DIR__ . '/resources/optimizer/cat1_jpegtran.jpg', $image->getBinaryContent());
    }
    
    public function testPngcrushOptimizer()
    {
        $binaryContent = file_get_contents(__DIR__ . '/resources/photos/cat2_original.png');
        $image = new Image($binaryContent);
        
        $optimizer = new PngcrushImageOptimizer();
        
        $optimizer->optimizeImage($image);
        
        $this->assertStringEqualsFile(__DIR__ . '/resources/optimizer/cat2_pngcrush.png', $image->getBinaryContent());
    }
}