<?php

namespace LibAxolotl\Groups;

use LibAxolotl\Groups\State\SenderKeyRecord;
use LibAxolotl\Groups\State\SenderKeyStore;
use LibAxolotl\Protocol\SenderKeyDistributionMessage;

class GroupSessionBuilder
{
    /**
     * @var SenderKeyStore $senderKeyStore
     */
    protected $senderKeyStore;

    public function __construct(SenderKeyStore $senderKeyStore)
    {
        $this->senderKeyStore = $senderKeyStore;
    }

    public function processSender($sender, $senderKeyDistributionMessage)
    {
        /** @var SenderKeyRecord $senderKeyRecord */
        $senderKeyRecord = $this->senderKeyStore->loadSenderKey($sender);

        $senderKeyRecord->addSenderKeyState($senderKeyDistributionMessage->getId(),
                                            $senderKeyDistributionMessage->getIteration(),
                                            $senderKeyDistributionMessage->getChainKey(),
                                            $senderKeyDistributionMessage->getSignatureKey());
        $this->senderKeyStore->storeSenderKey($sender, $senderKeyRecord);
    }

    public function process($groupId, $keyId, $iteration, $chainKey, $signatureKey)
    {
        /** @var SenderKeyRecord $senderKeyRecord */
        $senderKeyRecord = $this->senderKeyStore->loadSenderKey($groupId);

        $senderKeyRecord->setSenderKeyState($keyId, $iteration, $chainKey, $signatureKey);

        $this->senderKeyStore->storeSenderKey($groupId, $senderKeyRecord);

        return new SenderKeyDistributionMessage($keyId, $iteration, $chainKey, $signatureKey->getPublicKey());
    }
}
