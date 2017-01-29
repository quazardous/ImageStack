<?php
namespace ImageStack\Tests;

use ImageStack\Image;
use ImageStack\StorageBackend\FileStorageBackend;
use ImageStack\ImagePath;
use ImageStack\StorageBackend\OptimizedFileStorageBackend;
use ImageStack\ImageOptimizer\JpegtranImageOptimizer;

class StorageBackendTests extends \PHPUnit_Framework_TestCase
{
    
    public function testFileStorageBackend()
    {
        $originalPath = __DIR__ . '/resources/photos/cat1_original.jpg';
        $binaryContent = file_get_contents($originalPath);
        $image = new Image($binaryContent);
        
        $root = TESTDIR . '/basic';
        $fsb = new FileStorageBackend($root);
        
        $path = 'a/b/c/cat1.jpg';
        $imagePath = new ImagePath($path);
        $fsb->storeImage($image, $imagePath);
        
        $storedPath = $root . '/' . $imagePath->getPath();
        
        $this->assertFileExists($storedPath);
        $this->assertFileEquals($originalPath, $storedPath);
    }
    
    public function testOptimizedFileStorageBackend()
    {
        $originalPath = __DIR__ . '/resources/photos/cat1_original.jpg';
        $binaryContent = file_get_contents($originalPath);
        $image = new Image($binaryContent);
        
        $root = TESTDIR . '/optimized';
        $fsb = new OptimizedFileStorageBackend($root);
        
        $fsb->registerImageOptimizer('image/jpeg', new JpegtranImageOptimizer());
        
        $path = 'a/b/c/cat1.jpg';
        $imagePath = new ImagePath($path);
        $fsb->storeImage($image, $imagePath);
        
        $storedPath = $root . '/' . $imagePath->getPath();
        
        $this->assertFileExists($storedPath);
        $optimizedPath = __DIR__ . '/resources/optimizer/cat1_jpegtran.jpg';
        $this->assertFileEquals($optimizedPath, $storedPath);
    }
    
}