<?php

namespace LibAxolotl\Ecc;

interface ECPublicKey
{
    public function serialize();

    public function getType();
}
