<?php

namespace Mr\AventriSdk\Exception;

class InvalidCredentialsException extends AventriException
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct('Invalid credentials', 401);
    }
}