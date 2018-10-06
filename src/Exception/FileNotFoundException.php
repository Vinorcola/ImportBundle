<?php

namespace Vinorcola\ImportBundle\Exception;

use RuntimeException;
use Throwable;

class FileNotFoundException extends RuntimeException
{
    public function __construct(string $filePath, Throwable $previous = null)
    {
        parent::__construct('File "' . $filePath . '" not found.', 0, $previous);
    }
}
