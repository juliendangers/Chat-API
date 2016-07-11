<?php

namespace LibAxolotl\Exceptions;

class DuplicateMessageException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null) // [String s]
    {
        parent::__construct($message, $code, $previous);
    }
}
