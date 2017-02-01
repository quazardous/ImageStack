<?php
namespace ImageStack\ImageManipulator\Exception;

use ImageStack\Api\Exception\ImageManipulatorException as ApiImageManipulatorException;

class ImageManipulatorException extends ApiImageManipulatorException
{
    const CANNOT_MANIPULATE_IMAGE = 1;
}