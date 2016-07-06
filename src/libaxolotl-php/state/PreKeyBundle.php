<?php

namespace LibAxolotl\State;

use LibAxolotl\IdentityKey;
use LibAxolotl\Ecc\ECPublicKey;

class PreKeyBundle
{
    /**
     * @var int $registrationId
     */
    protected $registrationId;
    /**
     * @var int $deviceId
     */
    protected $deviceId;
    /**
     * @var int $preKeyId
     */
    protected $preKeyId;
    /**
     * @var ECPublicKey $preKeyPublic
     */
    protected $preKeyPublic;
    /**
     * @var int $signedPreKeyId
     */
    protected $signedPreKeyId;
    /**
     * @var ECPublicKey $signedPreKeyPublic
     */
    protected $signedPreKeyPublic;
    /**
     * @var mixed $signedPreKeySignature byte[]
     */
    protected $signedPreKeySignature;
    /**
     * @var IdentityKey $identityKey
     */
    protected $identityKey;

    public function PreKeyBundle($registrationId, $deviceId, $preKeyId, $preKeyPublic, $signedPreKeyId, $signedPreKeyPublic, $signedPreKeySignature, $identityKey) // [int registrationId, int deviceId, int preKeyId, ECPublicKey preKeyPublic, int signedPreKeyId, ECPublicKey signedPreKeyPublic, byte[] signedPreKeySignature, IdentityKey identityKey]
    {
        $this->registrationId = $registrationId;
        $this->deviceId = $deviceId;
        $this->preKeyId = $preKeyId;
        $this->preKeyPublic = $preKeyPublic;
        $this->signedPreKeyId = $signedPreKeyId;
        $this->signedPreKeyPublic = $signedPreKeyPublic;
        $this->signedPreKeySignature = $signedPreKeySignature;
        $this->identityKey = $identityKey;
    }

    public function getDeviceId()
    {
        return $this->deviceId;
    }

    public function getPreKeyId()
    {
        return $this->preKeyId;
    }

    public function getPreKey()
    {
        return $this->preKeyPublic;
    }

    public function getSignedPreKeyId()
    {
        return $this->signedPreKeyId;
    }

    public function getSignedPreKey()
    {
        return $this->signedPreKeyPublic;
    }

    public function getSignedPreKeySignature()
    {
        return $this->signedPreKeySignature;
    }

    public function getIdentityKey()
    {
        return $this->identityKey;
    }

    public function getRegistrationId()
    {
        return $this->registrationId;
    }
}
