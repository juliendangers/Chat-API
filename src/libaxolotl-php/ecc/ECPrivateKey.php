<?php

namespace LibAxolotl\Ecc;

interface ECPrivateKey
{
    public function serialize();

    public function getType();
}
