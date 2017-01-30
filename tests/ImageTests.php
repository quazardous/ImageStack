<?php
namespace ImageStack\Tests;

use ImageStack\Image;
use Imagine\Gd\Imagine;

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
    
    public function testImagineImage()
    {
        $filename = __DIR__ . '/resources/photos/cat1_original.jpg';
        $image = new Image(file_get_contents($filename));
        $this->assertEquals('image/jpeg', $image->getMimeType());
        $this->assertStringEqualsFile($filename, $image->getBinaryContent());
        
        $image->setImagine(new Imagine);
        $ii = $image->getImagineImage();
        $image->setImagineImage($ii, 'image/png');
        $this->assertStringNotEqualsFile($filename, $image->getBinaryContent());
    }
}