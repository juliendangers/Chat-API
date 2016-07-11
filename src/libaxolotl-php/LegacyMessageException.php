<?php

namespace LibAxolotl\Exceptions;

class LegacyMessageException extends \Exception
{
    public function __construct($detailMessage, $code = 0, \Exception $previous = null) // [String s]
    {
        parent::__construct($detailMessage, $code, $previous);
    }
}
