<?php

namespace Fintecture\Util;

class FintectureException extends \Exception
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        $message = 'Fintecture: ' . $message;

        parent::__construct($message, $code, $previous);
    }
}
