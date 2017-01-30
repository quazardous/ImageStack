<?php
namespace ImageStack\Exception;

use ImageStack\Api\Exception\ImageException as ApiImageException;

class ImageException extends ApiImageException
{
    const CANNOT_DETERMINE_MIME_TYPE = 1;
    const EMPTY_IMAGE = 2;
    const IMAGINE_NOT_SETUP = 3;
    const UNSUPPORTED_MIME_TYPE = 4;
}