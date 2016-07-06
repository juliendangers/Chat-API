<?php

namespace LibAxolotl\Exceptions;

class NoSessionException extends \Exception
{
    public function NoSessionException($s) // [String s]
    {
        $this->message = $s;
    }
}
