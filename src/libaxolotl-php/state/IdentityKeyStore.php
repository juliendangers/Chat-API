<?php

namespace LibAxolotl\State;

use LibAxolotl\IdentityKey;
use LibAxolotl\IdentityKeyPair;

abstract class IdentityKeyStore
{
    /**
     * @return IdentityKeyPair
     */
    abstract public function getIdentityKeyPair();

    abstract public function getLocalRegistrationId();

    /**
     * @param int $recipientId
     * @param IdentityKey $identityKey
     * @return void
     */
    abstract public function saveIdentity($recipientId, IdentityKey $identityKey);

    /**
     * @param int $recipientId
     * @param IdentityKey $identityKey
     * @return bool
     */
    abstract public function isTrustedIdentity($recipientId, IdentityKey $identityKey);
}
