<?php
namespace ImageStack\Api;

/**
 * API image path interface.
 *
 */
interface ImagePathInterface {
    /**
     * The path element of the image HTTP request (without stack prefix).
     * @return string
     */
    public function getPath();
    
    /**
     * The stack prefix.
     * The stack prefix is the part of the URL that is used to detect wich stack to trigger.
     * @return string|null
     */
    public function getPrefix();
}