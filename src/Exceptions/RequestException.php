<?php

namespace Bodoudou\SDK\Exceptions;

class RequestException extends SDKException
{
    protected $httpCode;

    public function __construct($message, $errorCode, $traceId, $httpCode = null)
    {
        parent::__construct($message, $errorCode, $traceId);
        if (!empty($httpCode)) {
            $this->httpCode = $httpCode;
        }
    }

    public function getHttpCode(): string
    {
        return $this->httpCode;
    }
}