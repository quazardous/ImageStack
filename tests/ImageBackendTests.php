<?php
namespace ImageStack\Tests;

use ImageStack\ImageBackend\FileImageBackend;
use ImageStack\ImagePath;
use ImageStack\ImageBackend\HttpImageBackend;
use ImageStack\ImageBackend\SequentialImageBackend;
use ImageStack\ImageBackend\CallbackImageBackend;
use ImageStack\Api\ImagePathInterface;
use ImageStack\Cache\RawFileCache;
use ImageStack\ImageBackend\CacheImageBackend;
use ImageStack\ImageBackend\PathRuleImageBackend;
use ImageStack\ImageBackend\PathRule\PatternPathRule;
use Imagine\Gd\Imagine;
use ImageStack\ImageManipulator\ConverterImageManipulator;
use ImageStack\Image;
use ImageStack\ImageBackend\ManipulatorImageBackend;

class ImageBackendTests extends \PHPUnit_Framework_TestCase
{ 
    public function testFileImageBackend()
    {
        $root = __DIR__ . '/resources';
        $fib = new FileImageBackend($root);
        
        $path = 'photos/cat1_original.jpg';
        $image = $fib->fetchImage(new ImagePath($path));
        
        $this->assertStringEqualsFile($root . '/' . $path, $image->getBinaryContent());
    }
    
    /**
     * @expectedException ImageStack\Api\Exception\ImageNotFoundException
     */
    public function testFileImageBackendNotFound()
    {
        $root = __DIR__ . '/resources';
        $fib = new FileImageBackend($root);
        
        $path = 'photos/XXX.jpg';
        $fib->fetchImage(new ImagePath($path));
    }
    
    public function testHttpImageBackend()
    {
        $rootUrl = sprintf('http://%s:%d/', WEB_SERVER_HOST, WEB_SERVER_PORT);
        $hib = new HttpImageBackend($rootUrl);
        
        $path = 'photos/cat1_original.jpg';
        $image = $hib->fetchImage(new ImagePath($path));
        
        $this->assertStringEqualsFile(WEB_SERVER_DOCROOT . '/' . $path, $image->getBinaryContent());
    }
    
    /**
     * @expectedException ImageStack\Api\Exception\ImageNotFoundException
     */
    public function testHttpImageBackendNotFound()
    {
        $rootUrl = sprintf('http://%s:%d/', WEB_SERVER_HOST, WEB_SERVER_PORT);
        $hib = new HttpImageBackend($rootUrl);
        
        $path = 'photos/XXX.jpg';
        $hib->fetchImage(new ImagePath($path));
    }
    
    public function testSequentialImageBackend()
    {
        $roots = [
            __DIR__ . '/resources/optimizer',
            __DIR__ . '/resources/photos',
        ];
        
        $siq = new SequentialImageBackend();
        foreach ($roots as $root) {
            $siq->addImageBackend(new FileImageBackend($root));
        }
        
        $path = 'cat1_original.jpg';
        $image = $siq->fetchImage(new ImagePath($path));
        
        $this->assertStringEqualsFile($roots[1] . '/' . $path, $image->getBinaryContent());
    }
    
    /**
     * @expectedException ImageStack\Api\Exception\ImageNotFoundException
     */
    public function testSequentialImageBackendNotFound()
    {
        $roots = [
            __DIR__ . '/resources/optimizer',
            __DIR__ . '/resources/photos',
        ];
        
        $siq = new SequentialImageBackend();
        foreach ($roots as $root) {
            $siq->addImageBackend(new FileImageBackend($root));
        }
        
        $path = 'XXX.jpg';
        $siq->fetchImage(new ImagePath($path));
    }
    
    public function testCallbackImageBackend()
    {
        $root = __DIR__ . '/resources';
        $fib = new FileImageBackend($root);
        
        $cib = new CallbackImageBackend(function (ImagePathInterface $path) use ($fib) {
           return $fib->fetchImage($path);
        });
        
        $path = 'photos/cat1_original.jpg';
        $image = $cib->fetchImage(new ImagePath($path));
        
        $this->assertStringEqualsFile($root . '/' . $path, $image->getBinaryContent());
    }
    
    public function testCacheImageBackend()
    {
        $root = __DIR__ . '/resources';
        $fib = new FileImageBackend($root);
        
        $cacheRoot = TESTDIR . '/cache_backend';
        $cache = new RawFileCache($cacheRoot);
        
        $cib = new CacheImageBackend($fib, $cache);
        
        // warmup our cache
        $path = 'photos/cat1_original.jpg';
        $image = $cib->fetchImage(new ImagePath($path));
        
        $this->assertStringEqualsFile($root . '/' . $path, $image->getBinaryContent());
        
        // bad backend
        $bib = new CallbackImageBackend(function (ImagePathInterface $path) {
           throw new \LogicException('No image');
        });
        
        // using our cache with the bad backend
        $cib = new CacheImageBackend($bib, $cache);
        
        // making evident bad backend throws exception for unknown image...
        $exception = false;
        try {
            $cib->fetchImage(new ImagePath('unknown.jpg'));
            $this->assertTrue(false);
        } catch (\LogicException $e) {
            $exception = true;
            $this->assertEquals('No image', $e->getMessage());
        }
        $this->assertTrue($exception);
        
        // now the cached image should avoid exception
        $image = $cib->fetchImage(new ImagePath($path));
        
        $this->assertStringEqualsFile($root . '/' . $path, $image->getBinaryContent());
        
    }
    
    public function testPathRuleImageBackend()
    {
        $root = __DIR__ . '/resources';
        $fib = new FileImageBackend($root);
        
        $rib = new PathRuleImageBackend($fib);
        $rib->addPathRule(new PatternPathRule('|^(style/big/)(.*)$|', [2]));
        
        $path = 'photos/cat1_original.jpg';
        $image = $rib->fetchImage(new ImagePath('style/big/' . $path));
        $this->assertStringEqualsFile($root . '/' . $path, $image->getBinaryContent());
    }
    
    public function testManipulatorImageBackend()
    {
        $root = __DIR__ . '/resources';
        $path = 'photos/cat1_original.jpg';

        $fib = new FileImageBackend($root);
        $image = $fib->fetchImage(new ImagePath($path));
        $this->assertEquals('image/jpeg', $image->getMimeType());
        
        $mib = new ManipulatorImageBackend($fib);
        $mib->addImageManipulator(new ConverterImageManipulator(new Imagine(), [
            'image/jpeg' => 'image/png',
        ]));
        $image = $mib->fetchImage(new ImagePath($path));
        $this->assertEquals('image/png', $image->getMimeType());
    }
}