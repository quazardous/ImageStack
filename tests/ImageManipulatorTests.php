<?php
namespace ImageStack\Tests;

use ImageStack\ImageManipulator\ConverterImageManipulator;
use Imagine\Gd\Imagine;
use ImageStack\Image;
use ImageStack\ImagePath;
use ImageStack\ImageManipulator\OptimizerImageManipulator;
use ImageStack\ImageOptimizer\JpegtranImageOptimizer;
use ImageStack\ImageManipulator\ThumbnailerImageManipulator;
use ImageStack\ImageManipulator\ThumbnailRule\PatternThumbnailRule;

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
    
    /**
     * @expectedException ImageStack\ImageManipulator\Exception\ImageManipulatorException
     * @expectedExceptionCode ImageStack\ImageManipulator\Exception\ImageManipulatorException::CANNOT_MANIPULATE_IMAGE
     */
    public function testThumbnailerManipulatorNoRules()
    {
        $tim = new ThumbnailerImageManipulator(new Imagine());
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $tim->manipulateImage($image, new ImagePath('test'));
    }
    
    /**
     * @expectedException ImageStack\Api\Exception\ImageNotFoundException
     */
    public function testThumbnailerManipulatorNotFound()
    {
        $rules = [
            '/NEVER/' => true,
            '/^test/' => false,
            '/test/' => true,
        ];
        
        $tim = new ThumbnailerImageManipulator(new Imagine());
        foreach ($rules as $pattern => $format) {
            $tim->addThumbnailRule(new PatternThumbnailRule($pattern, $format));
        }
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $tim->manipulateImage($image, new ImagePath('test'));
    }
    
    /**
     * @expectedException ImageStack\Api\Exception\ImageNotFoundException
     */
    public function testThumbnailerManipulatorNotFoundCallable()
    {
        $rules = [
            '/NEVER/' => true,
            '|^test/a|' => function($matches) { return false; },
            '/test/' => true,
        ];
        
        $tim = new ThumbnailerImageManipulator(new Imagine());
        foreach ($rules as $pattern => $format) {
            $tim->addThumbnailRule(new PatternThumbnailRule($pattern, $format));
        }
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $tim->manipulateImage($image, new ImagePath('test/abc'));
    }
    
    public function testThumbnailerManipulatorOriginal()
    {
        $rules = [
            '/NEVER/' => true,
            '|^test/a|' => true,
            '/test/' => true,
        ];
        
        $tim = new ThumbnailerImageManipulator(new Imagine());
        foreach ($rules as $pattern => $format) {
            $tim->addThumbnailRule(new PatternThumbnailRule($pattern, $format));
        }
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $image->setImagine(new Imagine());
        $tim->manipulateImage($image, new ImagePath('test/abc'));
        $box = [
            $image->getImagineImage()->getSize()->getWidth(),
            $image->getImagineImage()->getSize()->getHeight(),
        ];
        
        $this->assertEquals([1268, 1392], $box);
    }
    
    public function testThumbnailerManipulatorOutbound()
    {
        $rules = [
            '/NEVER/' => true,
            '|^test/a|' => '300x200',
            '/test/' => true,
        ];
        
        $tim = new ThumbnailerImageManipulator(new Imagine());
        foreach ($rules as $pattern => $format) {
            $tim->addThumbnailRule(new PatternThumbnailRule($pattern, $format));
        }
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $image->setImagine(new Imagine());
        $tim->manipulateImage($image, new ImagePath('test/abc'));
        $box = [
            $image->getImagineImage()->getSize()->getWidth(),
            $image->getImagineImage()->getSize()->getHeight(),
        ];
        
        $this->assertEquals([300, 200], $box);
    }
    
    public function testThumbnailerManipulatorInset()
    {
        $rules = [
            '/NEVER/' => true,
            '|^test/a|' => '<300x200',
            '/test/' => true,
        ];
        
        $tim = new ThumbnailerImageManipulator(new Imagine());
        foreach ($rules as $pattern => $format) {
            $tim->addThumbnailRule(new PatternThumbnailRule($pattern, $format));
        }
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $image->setImagine(new Imagine());
        $tim->manipulateImage($image, new ImagePath('test/abc'));
        $box = [
            $image->getImagineImage()->getSize()->getWidth(),
            $image->getImagineImage()->getSize()->getHeight(),
        ];
        
        $this->assertEquals([182, 200], $box);
    }
    
    public function testThumbnailerManipulatorSquare()
    {
        $rules = [
            '/NEVER/' => true,
            '|^test/a|' => '200',
            '/test/' => true,
        ];
        
        $tim = new ThumbnailerImageManipulator(new Imagine());
        foreach ($rules as $pattern => $format) {
            $tim->addThumbnailRule(new PatternThumbnailRule($pattern, $format));
        }
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $image->setImagine(new Imagine());
        $tim->manipulateImage($image, new ImagePath('test/abc'));
        $box = [
            $image->getImagineImage()->getSize()->getWidth(),
            $image->getImagineImage()->getSize()->getHeight(),
        ];
        
        $this->assertEquals([200, 200], $box);
    }
    
    public function testThumbnailerManipulatorCallbackFormat()
    {
        $rules = [
            '/NEVER/' => true,
            '|^test/([^/]+)/|' => function ($matches) { return $matches[1]; },
            '/test/' => true,
        ];
        
        $tim = new ThumbnailerImageManipulator(new Imagine());
        foreach ($rules as $pattern => $format) {
            $tim->addThumbnailRule(new PatternThumbnailRule($pattern, $format));
        }
        
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/cat1_original.jpg'));
        $image->setImagine(new Imagine());
        $tim->manipulateImage($image, new ImagePath('test/200x150/toto.jpg'));
        $box = [
            $image->getImagineImage()->getSize()->getWidth(),
            $image->getImagineImage()->getSize()->getHeight(),
        ];
        
        $this->assertEquals([200, 150], $box);
    }
    
}