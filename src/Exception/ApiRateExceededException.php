<?php

namespace App\Exception;

class ApiRateExceededException extends \Exception
{
    const DEFAULT_MESSAGE = 'API rate limit exceeded. Please wait before retrying.';
    const DEFAULT_CODE = 429;

    public function __construct(
        string $message = self::DEFAULT_MESSAGE,
        int $code = self::DEFAULT_CODE,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
