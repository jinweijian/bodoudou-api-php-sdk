<?php

namespace Bodoudou\SDK\Exceptions;

class GatewayTimeoutException extends RequestException
{
    protected $httpCode = 504;
}