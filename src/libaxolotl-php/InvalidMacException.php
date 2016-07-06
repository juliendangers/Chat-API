<?php

namespace LibAxolotl\Exceptions;

class InvalidMacException extends \Exception
{
    public function InvalidMacException($detailMessage) // [String detailMessage]
    {
        $this->message = $detailMessage;
    }
}
