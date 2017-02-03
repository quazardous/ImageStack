<?php
namespace ImageStack\ImageBackend\PathRule;

use ImageStack\Api\ImageInterface;
use ImageStack\Api\ImagePathInterface;

/**
 * Path rule interface.
 *
 */
interface PathRuleInterface
{
    /**
     * Apply a conditional path manipulation.
     * @param ImageInterface $image
     * @param ImagePathInterface $path
     * 
     * @return ImagePathInterface|null the new manipulated path or null if manipulation coud not by apply
     * NB: the original path reference should not be affected
     */
    public function createPath(ImagePathInterface $path);
}