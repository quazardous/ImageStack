<?php
namespace ImageStack\Tests;

use ImageStack\ImageManipulator\ConverterImageManipulator;
use Imagine\Gd\Imagine;
use ImageStack\Image;
use ImageStack\ImagePath;
use ImageStack\ImageManipulator\OptimizerImageManipulator;
use ImageStack\ImageOptimizer\JpegtranImageOptimizer;

class ImageManipulatorTests extends \PHPUnit_Framework_TestCase
{
    public function testConverterManipulator()
    {
        $cim = new ConverterImageManipulator(new Imagine(), [
            'image/jpeg' => 'image/png',
        ]);
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $this->assertEquals('image/jpeg', $image->getMimeType());
        
        $cim->manipulateImage($image, new ImagePath('test'));
        
        $this->assertEquals('image/png', $image->getMimeType());
    }
    
    public function testOptimizerManipulator()
    {
        $oim = new OptimizerImageManipulator();
        $oim->registerImageOptimizer(new JpegtranImageOptimizer());
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $oim->manipulateImage($image, new ImagePath('test'));
        
        $optimizedPath = __DIR__ . '/resources/optimizer/cat1_jpegtran.jpg';
        $this->assertStringEqualsFile($optimizedPath, $image->getBinaryContent());
    }
    
}