<?php

namespace Bodoudou\SDK\Exceptions;

class NotFoundException extends RequestException
{
    protected $httpCode = 404;

}