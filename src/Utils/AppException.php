<?php

namespace App\Utils;

use Exception;
use JsonSerializable;
use Throwable;

class AppException extends Exception implements JsonSerializable
{
    public const DEFAULT_MESSAGE = 'Unexpected error';
    public const DEFAULT_ERROR_CODE = '0';
    public const DEFAULT_STATUS_CODE = 500;

    private int $statusCode;

    public function __construct(
        string $message = self::DEFAULT_MESSAGE,
        string $errorCode = self::DEFAULT_ERROR_CODE,
        Throwable $previous = null,
        int $statusCode = self::DEFAULT_STATUS_CODE
    ) {
        parent::__construct($message, $errorCode, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function jsonSerialize(): array
    {
        return [
            'errorCode' => $this->code,
            'message'   => $this->message
        ];
    }
}
