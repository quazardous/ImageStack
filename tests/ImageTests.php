<?php
namespace ImageStack\Tests;

use ImageStack\Image;

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
    
}