<?php
namespace ImageStack\Tests;

use ImageStack\ImageBackend\FileImageBackend;
use ImageStack\ImagePath;
use ImageStack\ImageBackend\HttpImageBackend;

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
     * @expectedException ImageStack\ImageBackend\Exception\ImageBackendException
     * @expectedExceptionCode ImageStack\ImageBackend\Exception\ImageBackendException::IMAGE_NOT_FOUND
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
     * @expectedException ImageStack\ImageBackend\Exception\ImageBackendException
     * @expectedExceptionCode ImageStack\ImageBackend\Exception\ImageBackendException::IMAGE_NOT_FOUND
     */
    public function testHttpImageBackendNotFound()
    {
        $rootUrl = sprintf('http://%s:%d/', WEB_SERVER_HOST, WEB_SERVER_PORT);
        $hib = new HttpImageBackend($rootUrl);
        
        $path = 'photos/XXX.jpg';
        $hib->fetchImage(new ImagePath($path));
    }
    
}