<?php
namespace ImageStack\Cache\Exception;

class RawFileCacheException extends \RuntimeException
{
    const INVALID_CHARACTER = 1;
    const DIRECTORY_WITH_SAME_NAME = 2;
    const FILE_WITH_SAME_NAME = 3;
    const CANNOT_CREATE_DIRECTORY = 4;
    const CANNOT_WRITE_FILE = 5;
    const CANNOT_READ_FILE = 6;
    const CANNOT_UNLINK_FILE = 7;
}