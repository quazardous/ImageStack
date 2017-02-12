<?php
namespace ImageStack\Tests;

use ImageStack\ImageManipulator\WatermarkImageManipulator;
use Imagine\Gd\Imagine;
use ImageStack\Image;
use ImageStack\ImagePath;

class WatermarkImageManipulatorTests extends \PHPUnit_Framework_TestCase
{
    public function testIsBit()
    {
        $wim = new WatermarkImageManipulator(new Imagine(), 'test.png');
        
        $r = new \ReflectionMethod(WatermarkImageManipulator::class, 'isBit');
        $r->setAccessible(true);
        $this->assertTrue($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_TOP));
        $this->assertTrue($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_MIDDLE));
        $this->assertTrue($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_BOTTOM));
        $this->assertTrue($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_LEFT));
        $this->assertTrue($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_CENTER));
        $this->assertTrue($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_RIGHT));

        $this->assertFalse($r->invoke($wim, WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_TOP));
        $this->assertFalse($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_MIDDLE));
        $this->assertFalse($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_BOTTOM));
        $this->assertFalse($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_LEFT));
        $this->assertFalse($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_RIGHT,
            WatermarkImageManipulator::ANCHOR_CENTER));
        $this->assertFalse($r->invoke($wim, WatermarkImageManipulator::ANCHOR_TOP|WatermarkImageManipulator::ANCHOR_MIDDLE|WatermarkImageManipulator::ANCHOR_BOTTOM|WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_CENTER,
            WatermarkImageManipulator::ANCHOR_RIGHT));
    }
    
    protected function _testWatermarkManipulator($number, array $options, $watermark = __DIR__ . '/resources/watermark/tux-goku.png')
    {
        $wim = new WatermarkImageManipulator(new Imagine(), $watermark, $options);
        $image = new Image(file_get_contents(__DIR__ . '/resources/photos/penguins.jpg'));
        $wim->manipulateImage($image, new ImagePath('test'));
        $root = TESTDIR . '/watermark';
        if (!is_dir($root)) mkdir($root, 0755, true);
        $path = '/' . sprintf('%03d', $number) . '-penguins_watermark.jpg';
        file_put_contents($root . $path, $image->getBinaryContent());
        $this->assertFileExists($root . $path);
    }
    
    // not really tests but you can verify images with watermark in /tmp/ImageStackXYZ/watermark
    public function test000()
    {
        $this->_testWatermarkManipulator(0, []);
    }
    
    public function test001()
    {
        $this->_testWatermarkManipulator(1, ['anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP]);
    }

    public function test002()
    {
        $this->_testWatermarkManipulator(2, ['anchor' => WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_TOP]);
    }

    public function test003()
    {
        $this->_testWatermarkManipulator(3, ['anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_TOP]);
    }
    
    public function test004()
    {
        $this->_testWatermarkManipulator(4, ['anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_MIDDLE]);
    }

    public function test005()
    {
        $this->_testWatermarkManipulator(5, ['anchor' => WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_MIDDLE]);
    }

    public function test006()
    {
        $this->_testWatermarkManipulator(6, ['anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_MIDDLE]);
    }

    public function test007()
    {
        $this->_testWatermarkManipulator(7, ['anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_BOTTOM]);
    }

    public function test008()
    {
        $this->_testWatermarkManipulator(8, ['anchor' => WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_BOTTOM]);
    }

    public function test009()
    {
        $this->_testWatermarkManipulator(9, ['anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_BOTTOM]);
    }

    public function test010()
    {
        $this->_testWatermarkManipulator(10, [
            'anchor' => WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_MIDDLE,
            'repeat' => WatermarkImageManipulator::REPEAT_ALL,
        ]);
    }
    public function test011()
    {
        $this->_testWatermarkManipulator(11, [
            'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_TOP,
            'repeat' => WatermarkImageManipulator::REPEAT_ALL,
        ]);
    }
    public function test012()
    {
        $this->_testWatermarkManipulator(12, [
            'anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP,
            'repeat' => WatermarkImageManipulator::REPEAT_X,
        ]);
    }
    public function test013()
    {
        $this->_testWatermarkManipulator(13, [
            'anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP,
            'repeat' => WatermarkImageManipulator::REPEAT_Y,
        ]);
    }
    public function test014()
    {
        $this->_testWatermarkManipulator(14, [
            'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_TOP,
            'repeat' => WatermarkImageManipulator::REPEAT_X,
        ]);
    }
    public function test015()
    {
        $this->_testWatermarkManipulator(15, [
            'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_TOP,
            'repeat' => WatermarkImageManipulator::REPEAT_Y,
        ]);
    }
    public function test016()
    {
        $this->_testWatermarkManipulator(16, [
            'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_BOTTOM,
            'repeat' => WatermarkImageManipulator::REPEAT_X,
        ]);
    }
    public function test017()
    {
        $this->_testWatermarkManipulator(17, [
            'repeat' => WatermarkImageManipulator::REPEAT_X,
        ]);
    }
    public function test018()
    {
        $this->_testWatermarkManipulator(18, [
            'repeat' => WatermarkImageManipulator::REPEAT_Y,
        ]);
    }
    public function test019()
    {
        $this->_testWatermarkManipulator(19, [], __DIR__ . '/resources/watermark/do-not-copy.png');
    }
    public function test020()
    {
        $this->_testWatermarkManipulator(20, [
            'anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP,
        ], __DIR__ . '/resources/watermark/do-not-copy.png');
    }
    public function test021()
    {
        $this->_testWatermarkManipulator(21, [
            'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_BOTTOM,
        ], __DIR__ . '/resources/watermark/do-not-copy.png');
    }
    public function test022()
    {
        $this->_testWatermarkManipulator(22, [
            'anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP,
            'reduce' => WatermarkImageManipulator::REDUCE_INSET,
        ], __DIR__ . '/resources/watermark/do-not-copy.png');
    }
    public function test023()
    {
        $this->_testWatermarkManipulator(23, [
            'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_BOTTOM,
            'reduce' => WatermarkImageManipulator::REDUCE_INSET,
        ], __DIR__ . '/resources/watermark/do-not-copy.png');
    }
    public function test024()
    {
        $this->_testWatermarkManipulator(24, [
            'reduce' => WatermarkImageManipulator::REDUCE_INSET,
        ], __DIR__ . '/resources/watermark/do-not-copy.png');
    }
    public function test025()
    {
        $this->_testWatermarkManipulator(25, [
            'reduce' => WatermarkImageManipulator::REDUCE_OUTBOUND,
        ], __DIR__ . '/resources/watermark/do-not-copy.png');
    }

}