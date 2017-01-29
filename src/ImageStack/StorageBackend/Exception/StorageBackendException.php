<?php
namespace ImageStack\StorageBackend\Exception;

use ImageStack\Api\Exception\StorageBackendException as ApiStorageBackendException;

class StorageBackendException extends ApiStorageBackendException
{
    const CANNOT_WRITE_FILE = 1;
    const CANNOT_CREATE_DIR = 2;
}