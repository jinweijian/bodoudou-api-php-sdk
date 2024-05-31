<?php

namespace Bodoudou\SDK\Exceptions;

class BadGatewayException extends RequestException
{
    protected $httpCode = 502;

}