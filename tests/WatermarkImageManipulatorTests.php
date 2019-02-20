<?php
namespace ImageStack\Tests;

use ImageStack\ImageManipulator\WatermarkImageManipulator;
use Imagine\Gd\Imagine;
use ImageStack\Image;
use ImageStack\ImagePath;
use Imagine\Image\ImagineInterface;

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
    // = __DIR__ . '/resources/photos/penguins.jpg'
    protected function _testWatermarkManipulator($number, array $options, $target, ImagineInterface $imagine)
    {
        $watermark = $options['_watermark'] ?? __DIR__ . '/resources/watermark/tux-goku.png';
        $wim = new WatermarkImageManipulator($imagine, $watermark, $options);
        $image = new Image(file_get_contents($target));
        $infos = pathinfo($target);
        $wim->manipulateImage($image, new ImagePath('test'));
        $root = TESTDIR . '/watermark';
        if (!is_dir($root)) mkdir($root, 0755, true);
        $path = '/' . sprintf('%03d', $number) . '-' . $infos['filename'] . '-watermarked.' . $infos['extension'];
        file_put_contents($root . $path, $image->getBinaryContent());
        $this->assertFileExists($root . $path);
    }
    
    // not really tests but you can verify images with watermark in /tmp/ImageStackXYZ/watermark
    public function testGo()
    {
        $tests = [
            [],
            ['anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP],
            ['anchor' => WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_TOP],
            ['anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_TOP],
            ['anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_MIDDLE],
            ['anchor' => WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_MIDDLE],
            ['anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_MIDDLE],
            ['anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_BOTTOM],
            ['anchor' => WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_BOTTOM],
            ['anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_BOTTOM],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_CENTER|WatermarkImageManipulator::ANCHOR_MIDDLE,
                'repeat' => WatermarkImageManipulator::REPEAT_ALL,
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_TOP,
                'repeat' => WatermarkImageManipulator::REPEAT_ALL,
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP,
                'repeat' => WatermarkImageManipulator::REPEAT_X,
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP,
                'repeat' => WatermarkImageManipulator::REPEAT_Y,
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_TOP,
                'repeat' => WatermarkImageManipulator::REPEAT_X,
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_TOP,
                'repeat' => WatermarkImageManipulator::REPEAT_Y,
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_BOTTOM,
                'repeat' => WatermarkImageManipulator::REPEAT_X,
            ],[
                'repeat' => WatermarkImageManipulator::REPEAT_X,
            ],[
                'repeat' => WatermarkImageManipulator::REPEAT_Y,
            ],
            [
                '_watermark' => __DIR__ . '/resources/watermark/do-not-copy.png',
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP,
                '_watermark' => __DIR__ . '/resources/watermark/do-not-copy.png',
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_BOTTOM,
                '_watermark' => __DIR__ . '/resources/watermark/do-not-copy.png',
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_LEFT|WatermarkImageManipulator::ANCHOR_TOP,
                'reduce' => WatermarkImageManipulator::REDUCE_INSET,
                '_watermark' => __DIR__ . '/resources/watermark/do-not-copy.png',
            ],
            [
                'anchor' => WatermarkImageManipulator::ANCHOR_RIGHT|WatermarkImageManipulator::ANCHOR_BOTTOM,
                'reduce' => WatermarkImageManipulator::REDUCE_INSET,
                '_watermark' => __DIR__ . '/resources/watermark/do-not-copy.png',
            ],
            [
                'reduce' => WatermarkImageManipulator::REDUCE_INSET,
                '_watermark' => __DIR__ . '/resources/watermark/do-not-copy.png',
            ],
            [
                'reduce' => WatermarkImageManipulator::REDUCE_OUTBOUND,
                '_watermark' => __DIR__ . '/resources/watermark/do-not-copy.png',
            ],
        ];
        
        foreach ($tests as $i => $test) {
            foreach ([__DIR__ . '/resources/photos/penguins.jpg' => new Imagine(), __DIR__ . '/resources/photos/animated.gif' => new \Imagine\Imagick\Imagine()] as $target => $imagine) {
                $this->_testWatermarkManipulator($i, $test, $target, $imagine);
            }
        }
    }

}