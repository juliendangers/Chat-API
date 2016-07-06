<?php

namespace LibAxolotl\Exceptions;

class InvalidVersionException extends \Exception
{
    public function InvalidVersionException($detailMessage) // [String detailMessage]
    {
        $this->message = $detailMessage;
    }
}
