<?php

namespace LibAxolotl;

use LibAxolotl\Ecc\Curve;
use LibAxolotl\Ecc\ECPrivateKey;

use Textsecure\IdentityKeyPairStructure as Textsecure_IdentityKeyPairStructure;

class IdentityKeyPair
{
    /** @var IdentityKey $publicKey */
    protected $publicKey;
    /** @var ECPrivateKey $privateKey */
    protected $privateKey;

    public function __construct(IdentityKey $publicKey = null, ECPrivateKey $privateKey = null, $serialized = null)
    {
        if ($serialized == null) {
            $this->publicKey = $publicKey;
            $this->privateKey = $privateKey;
        } else {
            $structure = new Textsecure_IdentityKeyPairStructure();
            $structure->parseFromString($serialized);
            $this->publicKey = new IdentityKey($structure->getPublicKey(), 0);
            $this->privateKey = Curve::decodePrivatePoint($structure->getPrivateKey());
        }
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function serialize()
    {
        $struct = new Textsecure_IdentityKeyPairStructure();

        return $struct->setPublicKey((string) $this->publicKey->serialize())->setPrivateKey((string) $this->privateKey->serialize())->serializeToString();
    }
}
