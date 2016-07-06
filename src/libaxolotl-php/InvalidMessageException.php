<?php

namespace LibAxolotl\Exceptions;

class InvalidMessageException extends \Exception
{
    public function InvalidMessageException($detailMessage, $throw = null) // [String detailMessage]
    {
        $this->message = $detailMessage;
        if ($throw != null) {
            $this->previous = $throw;
        }
    }
}
