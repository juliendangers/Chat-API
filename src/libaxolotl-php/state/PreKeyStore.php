<?php

namespace LibAxolotl\State;

use LibAxolotl\Exceptions\InvalidKeyIdException;

abstract class PreKeyStore
{
    /**
     * @param int $preKeyId
     * @throws InvalidKeyIdException
     * @return PreKeyRecord
     */
    abstract public function loadPreKey($preKeyId);

    /**
     * @param int $preKeyId
     * @param PreKeyRecord $record
     * @return void
     */
    abstract public function storePreKey($preKeyId, PreKeyRecord $record);

    /**
     * @param int $preKeyId
     * @return bool
     */
    abstract public function containsPreKey($preKeyId);

    /**
     * @param int $preKeyId
     * @return void
     */
    abstract public function removePreKey($preKeyId);
}
