<?php

namespace Bodoudou\SDK\Exceptions;

class InternalServerErrorException extends RequestException
{
    protected $httpCode = 500;
}