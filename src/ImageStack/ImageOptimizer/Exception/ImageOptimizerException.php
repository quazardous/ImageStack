<?php
namespace ImageStack\ImageOptimizer\Exception;

use ImageStack\Api\Exception\ImageStackException;

class ImageOptimizerException extends ImageStackException
{
    const CANNOT_READ_TMPFILE = 1;
    const CANNOT_WRITE_TMPFILE = 2;
    const EXEC_ERROR = 3;
    const UNSUPPORTED_MIME_TYPE = 4;
}