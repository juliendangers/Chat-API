<?php

namespace LibAxolotl\State;

use LibAxolotl\Ecc\ECPublicKey;

use LibAxolotl\Ecc\Curve;
use LibAxolotl\Ecc\ECPrivateKey;
use LibAxolotl\Ecc\ECKeyPair;
use \Exception as Exception;

class PreKeyRecord
{
    protected $structure;    // PreKeyRecordStructure

    public function __construct($id = null, ECKeyPair $keyPair = null, $serialized = null) // [int id, ECKeyPair keyPair]
    {
        $this->structure = new Textsecure_PreKeyRecordStructure();
        if ($serialized == null) {
            $this->structure->setId($id)->setPublicKey((string) $keyPair->getPublicKey()->serialize())->setPrivateKey((string) $keyPair->getPrivateKey()->serialize());
        } else {
            try {
                $this->structure->parseFromString($serialized);
            } catch (Exception $ex) {
                throw new Exception('Cannot unserialize PreKEyRecordStructure');
            }
        }
    }

    public function getId()
    {
        return $this->structure->getId();
    }

    public function getKeyPair()
    {
        /** @var ECPublicKey $publicKey */
        $publicKey = Curve::decodePoint($this->structure->getPublicKey(), 0);
        /** @var ECPrivateKey $privateKey */
        $privateKey = Curve::decodePrivatePoint($this->structure->getPrivateKey());

        return new ECKeyPair($publicKey, $privateKey);
    }

    public function serialize()
    {
        return $this->structure->serializeToString();
    }
}
