<?php

namespace LibAxolotl\Tests\Groups;

use LibAxolotl\Groups\State\SenderKeyStore;
use LibAxolotl\Groups\State\SenderKeyRecord;

class InMemorySenderKeyStore extends SenderKeyStore
{
    protected $store;

    public function __construct()
    {
        $this->store = [];
    }

    public function storeSenderKey($senderKeyId, $senderKeyRecord)
    {
        $this->store[$senderKeyId] = $senderKeyRecord;
    }

    public function loadSenderKey($senderKeyId)
    {
        if (isset($this->store[$senderKeyId])) {
            return new SenderKeyRecord($this->store[$senderKeyId]->serialize());
        }

        return new SenderKeyRecord();
    }
}
