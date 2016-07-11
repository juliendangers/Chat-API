<?php

namespace LibAxolotl\Exceptions;

class InvalidMacException extends \Exception
{
    public function __construct($detailMessage, $code = 0, \Exception $previous = null) // [String detailMessage]
    {
        parent::__construct($detailMessage, $code, $previous);
    }
}
