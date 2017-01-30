<?php
namespace ImageStack\Tests;

use ImageStack\StorageBackend\FileStorageBackend;
use ImageStack\ImageManipulator\OptimizerImageManipulator;
use ImageStack\ImageOptimizer\JpegtranImageOptimizer;
use ImageStack\ImageBackend\FileImageBackend;
use ImageStack\ImageStack;
use ImageStack\ImagePath;

class ImageStackTests extends \PHPUnit_Framework_TestCase
{
    public function testImageStack()
    {
        $imageRoot = __DIR__ . '/resources';
        $storageRoot = TESTDIR . '/stack';
        
        $oim = new OptimizerImageManipulator();
        $oim->registerImageOptimizer(new JpegtranImageOptimizer());
        
        $stack = new ImageStack(new FileImageBackend($imageRoot), new FileStorageBackend($storageRoot));
        $stack->addImageManipulator($oim);
        
        $path = 'photos/cat1_original.jpg';
        
        $image = $stack->stackImage(new ImagePath($path));
        
        $optimizedPath = __DIR__ . '/resources/optimizer/cat1_jpegtran.jpg';
        $this->assertStringEqualsFile($optimizedPath, $image->getBinaryContent());

    }
}