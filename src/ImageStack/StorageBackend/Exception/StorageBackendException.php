<?php
namespace ImageStack\StoageBackend\Exception;

use ImageStack\Api\Exception\StorageBackendException as ApiStorageBackendException;

class StorageBackendException extends ApiStorageBackendException
{
    const CANNOT_WRITE_FILE = 1;
    const CANNOT_READ_TMPFILE = 2;
    const CANNOT_WRITE_TMPFILE = 3;
    const EXEC_ERROR = 4;
}