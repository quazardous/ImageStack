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
        $root = TESTDIR . '/optimized';
        $fsb = new OptimizedFileStorageBackend($root);
        $fsb->registerImageOptimizer(new JpegtranImageOptimizer());

        // supported MIME
        $originalPath = __DIR__ . '/resources/photos/cat1_original.jpg';
        $binaryContent = file_get_contents($originalPath);
        $image = new Image($binaryContent);
        $path = 'a/b/c/cat1.jpg';
        $imagePath = new ImagePath($path);
        $fsb->storeImage($image, $imagePath);
        $storedPath = $root . '/' . $imagePath->getPath();
        $this->assertFileExists($storedPath);
        $optimizedPath = __DIR__ . '/resources/optimizer/cat1_jpegtran.jpg';
        $this->assertFileEquals($optimizedPath, $storedPath);
        
        // unsupported MIME
        $originalPath = __DIR__ . '/resources/photos/cat2_original.png';
        $binaryContent = file_get_contents($originalPath);
        $image = new Image($binaryContent);
        $path = 'a/b/c/cat2.png';
        $imagePath = new ImagePath($path);
        $fsb->storeImage($image, $imagePath);
        $storedPath = $root . '/' . $imagePath->getPath();
        $this->assertFileExists($storedPath);
        $this->assertFileEquals($originalPath, $storedPath);
    }
    
}