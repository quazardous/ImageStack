<?php
namespace ImageStack\Tests;

use ImageStack\Image;
use Imagine\Image\ImagineInterface;

class ImageTests extends \PHPUnit_Framework_TestCase
{
    public function testMimeDetection()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $this->assertEquals('image/jpeg', $image->getMimeType());
        
        $binaryContent = file_get_contents(__DIR__ . '/resources/photos/cat2_original.png');
        
        $image->setBinaryContent($binaryContent);
        $this->assertEquals('image/png', $image->getMimeType());

        $image->setBinaryContent($binaryContent, 'image/dummy');
        $this->assertEquals('image/dummy', $image->getMimeType());
    }
    
    /**
     * @expectedException ImageStack\Exception\ImageException
     * @expectedExceptionCode ImageStack\Exception\ImageException::EMPTY_IMAGE
     */
    public function testEmptyImage()
    {
        new Image('');
    }
    
    /**
     * @expectedException ImageStack\Exception\ImageException
     * @expectedExceptionCode ImageStack\Exception\ImageException::IMAGINE_NOT_SETUP
     */
    public function testImagineImageNotSetup()
    {
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $this->assertEquals('image/jpeg', $image->getMimeType());
        
        $image->getImagineImage();
    }
    
    protected function _testImagineImage(ImagineInterface $imagine)
    {
        $filename = __DIR__ . '/resources/photos/cat1_original.jpg';
        $image = new Image(file_get_contents($filename));
        $this->assertEquals('image/jpeg', $image->getMimeType());
        $this->assertStringEqualsFile($filename, $image->getBinaryContent());
        
        $image->setImagine($imagine);
        $ii = $image->getImagineImage();
        $image->setImagineImage($ii, 'image/png');
        $this->assertStringNotEqualsFile($filename, $image->getBinaryContent());
        
        $filename = __DIR__ . '/resources/optimizer/cat1_jpegtran.jpg';
        $image = new Image(file_get_contents($filename));
        $this->assertEquals('image/jpeg', $image->getMimeType());
        $this->assertStringEqualsFile($filename, $image->getBinaryContent());
        
        $filename = __DIR__ . '/resources/optimizer/animated.gif';
        $image = new Image(file_get_contents($filename));
        $this->assertEquals('image/gif', $image->getMimeType());
        $this->assertStringEqualsFile($filename, $image->getBinaryContent());
        $imagine = new \Imagine\Gd\Imagine();
        
    }
    
    public function testAllImagineImage()
    {
        $this->_testImagineImage(new \Imagine\Gd\Imagine);
        if (class_exists('Gmagick')) {
            $this->_testImagineImage(new \Imagine\Gmagick\Imagine);
        }
        if (class_exists('Imagick')) {
            $this->_testImagineImage(new \Imagine\Imagick\Imagine);
        }
    }
    
}