<?php

namespace LibAxolotl\Exceptions;

class DuplicateMessageException extends \Exception
{
    public function DuplicateMessageException($s) // [String s]
    {
        $this->message = $s;
    }
}
