<?php

namespace LibAxolotl\Tests;

use LibAxolotl\State\PreKeyRecord;
use LibAxolotl\State\PreKeyStore;
use LibAxolotl\Exceptions\InvalidKeyIdException;

class InMemoryPreKeyStore extends PreKeyStore
{
    protected $store;

    public function __construct()
    {
        $this->store = [];
    }

    public function loadPreKey($preKeyId)
    {
        if (!isset($this->store[$preKeyId])) {
            throw new InvalidKeyIdException('No such prekeyRecord!');
        }

        return new PreKeyRecord(null, null, $this->store[$preKeyId]);
    }

    public function storePreKey($preKeyId, PreKeyRecord $preKeyRecord)
    {
        $this->store[$preKeyId] = $preKeyRecord->serialize();
    }

    public function containsPreKey($preKeyId)
    {
        return isset($this->store[$preKeyId]);
    }

    public function removePreKey($preKeyId)
    {
        if (isset($this->store[$preKeyId])) {
            unset($this->store[$preKeyId]);
        }
    }
}
