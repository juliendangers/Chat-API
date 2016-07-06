<?php

namespace LibAxolotl;

use LibAxolotl\Ecc\Curve;
use LibAxolotl\Ecc\ECPublicKey;

class IdentityKey
{
    /**
     * @var ECPublicKey $publicKey
     */
    protected $publicKey;

    /**
     * @param ECPublicKey $publicKeyOrBytes
     * @param int $offset
     * @throws Exceptions\InvalidKeyException
     */
    public function IdentityKey($publicKeyOrBytes, $offset = null)
    {
        if ($offset === null) {
            $this->publicKey = $publicKeyOrBytes;
        } else {
            $this->publicKey = Curve::decodePoint($publicKeyOrBytes, $offset);
        }
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function serialize()
    {
        return $this->publicKey->serialize();
    }

    public function getFingerprint()
    {
        $hex = unpack('H*', $this->publicKey->serialize());
        $hex = implode(' ', str_split($hex, 2));

        return $hex;
    }

    public function equals($other) // [Object other]
    {
        if (($other == null)) {
            return  false;
        }
        if (!($other instanceof self)) {
            return  false;
        }

        return $this->publicKey->equals($other->getPublicKey());
    }

    public function hashCode()
    {
        return $this->publicKey->hashCode();
    }
}
