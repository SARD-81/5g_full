<?php

namespace Modules\Server\Exceptions;

use Exception;

class CommandExecutionException extends Exception
{
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly array $details = [],
        public readonly int $httpStatus = 422,
        ?Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}
