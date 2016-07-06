<?php

namespace LibAxolotl\Exceptions;

class LegacyMessageException extends \Exception
{
    public function LegacyMessageException($detailMesssage) // [String s]
    {
        $this->message = $detailMesssage;
    }
}
