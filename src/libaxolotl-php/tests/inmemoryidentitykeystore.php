<?php

namespace LibAxolotl\Tests;

use LibAxolotl\Ecc\Curve;
use LibAxolotl\IdentityKey;
use LibAxolotl\IdentityKeyPair;
use LibAxolotl\State\IdentityKeyStore;
use LibAxolotl\Utils\KeyHelper;

class InMemoryIdentityKeyStore extends IdentityKeyStore
{
    protected $trustedKeys;
    protected $identityKeyPair;
    protected $localRegistrationId;

    public function __construct()
    {
        $this->trustedKeys = [];
        $identityKeyPairKeys = Curve::generateKeyPair();
        $this->identityKeyPair = new IdentityKeyPair(new IdentityKey($identityKeyPairKeys->getPublicKey()),
                                               $identityKeyPairKeys->getPrivateKey());
        $this->localRegistrationId = KeyHelper::generateRegistrationId();
    }

    public function getIdentityKeyPair()
    {
        return $this->identityKeyPair;
    }

    public function getLocalRegistrationId()
    {
        return $this->localRegistrationId;
    }

    public function saveIdentity($recepientId, IdentityKey $identityKey)
    {
        $this->trustedKeys[$recepientId] = $identityKey;
    }

    public function isTrustedIdentity($recepientId, IdentityKey $identityKey)
    {
        if (!isset($this->trustedKeys[$recepientId])) {
            return true;
        }

        return $this->trustedKeys[$recepientId] == $identityKey;
    }
}
