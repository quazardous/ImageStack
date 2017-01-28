<?php
namespace ImageStack\ImageBackend\Exception;

use ImageStack\Api\Exception\ImageBackendException as ApiImageBackendException;

class ImageBackendException extends ApiImageBackendException
{
    const CANNOT_READ_FILE = 1;
}