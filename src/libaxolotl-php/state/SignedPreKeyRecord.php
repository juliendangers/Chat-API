<?php

namespace LibAxolotl\State;

use LibAxolotl\Ecc\Curve;
use LibAxolotl\Ecc\ECKeyPair;
use LibAxolotl\Ecc\ECPublicKey;
use LibAxolotl\Ecc\ECPrivateKey;

use LibAxolotl\Exceptions\InvalidKeyException;
use \Exception as Exception;

class SignedPreKeyRecord
{
    protected $structure;

    public function SignedPreKeyRecord($id = null, $timestamp = null, ECKeyPair $keyPair = null, $signature = null, $serialized = null) // [int id, long timestamp, ECKeyPair keyPair, byte[] signature]
    {
        $struct = new Textsecure_SignedPreKeyRecordStructure();
        if ($serialized == null) {
            $struct->setId($id);
            $struct->setPublicKey((string) $keyPair->getPublicKey()->serialize());
            $struct->setPrivateKey((string) $keyPair->getPrivateKey()->serialize());
            $struct->setSignature((string) $signature);
            $struct->setTimestamp($timestamp);
        } else {
            $struct->parseFromString($serialized);
        }
        $this->structure = $struct; //$SignedPreKeyRecordStructure->newBuilder()->setId($id)->setPublicKey($ByteString->copyFrom($keyPair->getPublicKey()->serialize()))->setPrivateKey($ByteString->copyFrom($keyPair->getPrivateKey()->serialize()))->setSignature($ByteString->copyFrom($signature))->setTimestamp($timestamp)->build();
    }

    public function getId()
    {
        return $this->structure->getId();
    }

    public function getTimestamp()
    {
        return $this->structure->getTimestamp();
    }

    public function getKeyPair()
    {
        try {
            /** @var ECPublicKey $publicKey */
            $publicKey = Curve::decodePoint($this->structure->getPublicKey(), 0);
            /** @var ECPrivateKey $privateKey */
            $privateKey = Curve::decodePrivatePoint($this->structure->getPrivateKey());

            return  new ECKeyPair($publicKey, $privateKey);
        } catch (InvalidKeyException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getSignature()
    {
        return $this->structure->getSignature();
    }

    public function serialize()
    {
        return $this->structure->serializeToString();
    }
}
