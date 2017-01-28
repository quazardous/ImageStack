<?php
namespace ImageStack;

use ImageStack\Api\Exception\ImageException as ApiImageException;

class ImageException extends ApiImageException
{
    const CANNOT_DETERMINE_MIME_TYPE = 1;
}