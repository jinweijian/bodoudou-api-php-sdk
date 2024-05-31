<?php

namespace Bodoudou\SDK\Exceptions;

class RequestTimeoutException extends RequestException
{
    protected $httpCode = 408;

}