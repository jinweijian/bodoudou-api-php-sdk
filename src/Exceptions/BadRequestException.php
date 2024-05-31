<?php

namespace Bodoudou\SDK\Exceptions;

class BadRequestException extends RequestException
{
    protected $httpCode = 400;

}