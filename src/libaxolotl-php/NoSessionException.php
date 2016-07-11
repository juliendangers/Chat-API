<?php

namespace LibAxolotl\Exceptions;

class NoSessionException extends \Exception
{
    public function NoSessionException($message, $code = 0, \Exception $previous = null) // [String s]
    {
        parent::__construct($message, $code, $previous);
    }
}
