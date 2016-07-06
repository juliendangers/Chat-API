<?php

// coding=utf-8

namespace LibAxolotl\Tests;

use LibAxolotl\Protocol\CiphertextMessage;
use LibAxolotl\Protocol\WhisperMessage;
use LibAxolotl\Protocol\KeyExchangeMessage;

use LibAxolotl\State\PreKeyBundle;
use LibAxolotl\State\PreKeyRecord;
use LibAxolotl\State\SignedPreKeyRecord;

use LibAxolotl\Exceptions\UntrustedIdentityException;

use LibAxolotl\SessionBuilder;
use LibAxolotl\SessionCipher;

function parseText($txt)
{
    for ($x = 0; $x < strlen($txt); $x++) {
        if (ord($txt[$x]) < 20 || ord($txt[$x]) > 230) {
            $txt = 'HEX:'.bin2hex($txt);

            return $txt;
        }
    }

    return $txt;
}
function niceVarDump($obj, $ident = 0)
{
    $data = '';
    $data .= str_repeat(' ', $ident);
    $original_ident = $ident;
    $toClose = false;
    switch (gettype($obj)) {
        case 'object':
            $vars = (array) $obj;
            $data .= gettype($obj).' ('.get_class($obj).') ('.count($vars).") {\n";
            $ident += 2;
            foreach ($vars as $key => $var) {
                $type = '';
                $k = bin2hex($key);
                if (strpos($k, '002a00') === 0) {
                    $k = str_replace('002a00', '', $k);
                    $type = ':protected';
                } elseif (strpos($k, bin2hex("\x00".get_class($obj)."\x00")) === 0) {
                    $k = str_replace(bin2hex("\x00".get_class($obj)."\x00"), '', $k);
                    $type = ':private';
                }
                $k = hex2bin($k);
                if (is_subclass_of($obj, 'ProtobufMessage') && $k == 'values') {
                    $r = new \ReflectionClass($obj);
                    $constants = $r->getConstants();
                    $newVar = [];
                    foreach ($constants as $ckey => $cval) {
                        if (substr($ckey, 0, 3) != 'PB_') {
                            $newVar[$ckey] = $var[$cval];
                        }
                    }
                    $var = $newVar;
                }
                $data .= str_repeat(' ', $ident)."[$k$type]=>\n".niceVarDump($var, $ident)."\n";
            }
            $toClose = true;
        break;
        case 'array':
            $data .= 'array ('.count($obj).") {\n";
            $ident += 2;
            foreach ($obj as $key => $val) {
                $data .= str_repeat(' ', $ident).'['.(is_int($key) ? $key : "\"$key\"")."]=>\n".niceVarDump($val, $ident)."\n";
            }
            $toClose = true;
        break;
        case 'string':
            $data .= 'string "'.parseText($obj)."\"\n";
        break;
        case 'NULL':
            $data .= gettype($obj);
        break;
        default:
            $data .= gettype($obj).'('.strval($obj).")\n";
        break;
    }
    if ($toClose) {
        $data .= str_repeat(' ', $original_ident)."}\n";
    }

    return $data;
}
class SessionBuilderTest extends \PHPUnit_Framework_TestCase
{
    const ALICE_RECIPIENT_ID = 5;
    const BOB_RECIPIENT_ID = 2;

    public function test_basicKeyExchange()
    {
        $aliceStore = new InMemoryAxolotlStore();
        $aliceSessionBuilder = new SessionBuilder($aliceStore, $aliceStore, $aliceStore, $aliceStore, self::BOB_RECIPIENT_ID, 1);

        $bobStore = new InMemoryAxolotlStore();
        $bobSessionBuilder = new SessionBuilder($bobStore, $bobStore, $bobStore, $bobStore, self::ALICE_RECIPIENT_ID, 1);

        $aliceKeyExchangeMessage = $aliceSessionBuilder->processInitKeyExchangeMessage();
        $this->assertTrue($aliceKeyExchangeMessage != null);

        $aliceKeyExchangeMessageBytes = $aliceKeyExchangeMessage->serialize();

        $bobKeyExchangeMessage = $bobSessionBuilder->processKeyExchangeMessage(
                                                                            new KeyExchangeMessage(null, null, null, null, null, null, null, $aliceKeyExchangeMessageBytes));

        $this->assertTrue($bobKeyExchangeMessage != null);

        define('TEST', true);
        $bobKeyExchangeMessageBytes = $bobKeyExchangeMessage->serialize();
        $response = $aliceSessionBuilder->processKeyExchangeMessage(new KeyExchangeMessage(null, null, null, null, null, null, null, $bobKeyExchangeMessageBytes));

        $this->assertTrue($response == null);
        $this->assertTrue($aliceStore->containsSession(self::BOB_RECIPIENT_ID, 1));
        $this->assertTrue($bobStore->containsSession(self::ALICE_RECIPIENT_ID, 1));

        $this->runInteraction($aliceStore, $bobStore);

        $aliceStore = new InMemoryAxolotlStore();
        $aliceSessionBuilder = new SessionBuilder($aliceStore, $aliceStore, $aliceStore, $aliceStore, self::BOB_RECIPIENT_ID, 1);
        $aliceKeyExchangeMessage = $aliceSessionBuilder->processInitKeyExchangeMessage();

        try {
            $bobKeyExchangeMessage = $bobSessionBuilder->processKeyExchangeMessage($aliceKeyExchangeMessage);
            throw new \AssertionError("This identity shouldn't be trusted!");
        } catch (UntrustedIdentityException $ex) {
            $bobStore->saveIdentity(self::ALICE_RECIPIENT_ID, $aliceKeyExchangeMessage->getIdentityKey());
        }
        $bobKeyExchangeMessage = $bobSessionBuilder->processKeyExchangeMessage($aliceKeyExchangeMessage);

        $this->assertTrue($aliceSessionBuilder->processKeyExchangeMessage($bobKeyExchangeMessage) == null);

        self.runInteraction($aliceStore, $bobStore);
    }

    public function runInteraction($aliceStore, $bobStore)
    {
        /*
        :type aliceStore: AxolotlStore
        :type  bobStore: AxolotlStore
        */

        $aliceSessionCipher = new SessionCipher($aliceStore, $aliceStore, $aliceStore, $aliceStore, self::BOB_RECIPIENT_ID, 1);
        $bobSessionCipher = new SessionCipher($bobStore, $bobStore, $bobStore, $bobStore, self::ALICE_RECIPIENT_ID, 1);

        $originalMessage = 'smert ze smert';
        $aliceMessage = $aliceSessionCipher->encrypt($originalMessage);

        $this->assertTrue($aliceMessage->getType() == CiphertextMessage::WHISPER_TYPE);
        $plaintext = $bobSessionCipher->decryptMsg(new WhisperMessage(null, null, null, null, null, null, null, null, $aliceMessage->serialize()));
        $this->assertEquals($plaintext, $originalMessage);

        $bobMessage = $bobSessionCipher->encrypt($originalMessage);

        $this->assertTrue($bobMessage->getType() == CiphertextMessage::WHISPER_TYPE);

        $plaintext = $aliceSessionCipher->decryptMsg(new WhisperMessage(null, null, null, null, null, null, null, null, $bobMessage->serialize()));
        $this->assertEquals($plaintext, $originalMessage);

        for ($i = 0; $i < 10; $i++) {
            $loopingMessage = 'What do we mean by saying that existence precedes essence? '.
                             'We mean that man first of all exists, encounters himself, '.
                             'surges up in the world--and defines himself aftward. '.$i;
            $aliceLoopingMessage = $aliceSessionCipher->encrypt($loopingMessage);
            $loopingPlaintext = $bobSessionCipher->decryptMsg(new WhisperMessage(null, null, null, null, null, null, null, null, $aliceLoopingMessage->serialize()));
            $this->assertEquals($loopingPlaintext, $loopingMessage);
        }

        for ($i = 0; $i < 10; $i++) {
            $loopingMessage = 'What do we mean by saying that existence precedes essence? '.
                 'We mean that man first of all exists, encounters himself, '.
                 'surges up in the world--and defines himself aftward. '.$i;
            $bobLoopingMessage = $bobSessionCipher->encrypt($loopingMessage);

            $loopingPlaintext = $aliceSessionCipher->decryptMsg(new WhisperMessage(null, null, null, null, null, null, null, null, $bobLoopingMessage->serialize()));
            $this->assertEquals($loopingPlaintext, $loopingMessage);
        }
        $aliceOutOfOrderMessages = [];

        for ($i = 0; $i < 10; $i++) {
            $loopingMessage = 'What do we mean by saying that existence precedes essence? '.
                 'We mean that man first of all exists, encounters himself, '.
                 'surges up in the world--and defines himself aftward. '.$i;
            $aliceLoopingMessage = $aliceSessionCipher->encrypt($loopingMessage);
            $aliceOutOfOrderMessages[] = [$loopingMessage, $aliceLoopingMessage];
        }
        for ($i = 0; $i < 10; $i++) {
            $loopingMessage = 'What do we mean by saying that existence precedes essence? '.
                 'We mean that man first of all exists, encounters himself, '.
                 'surges up in the world--and defines himself aftward.'.$i;
            $aliceLoopingMessage = $aliceSessionCipher->encrypt($loopingMessage);
            $loopingPlaintext = $bobSessionCipher->decryptMsg(new WhisperMessage(null, null, null, null, null, null, null, null, $aliceLoopingMessage->serialize()));
            $this->assertEquals($loopingPlaintext, $loopingMessage);
        }
        for ($i = 0; $i < 10; $i++) {
            $loopingMessage = 'You can only desire based on what you know: '.$i;
            $bobLoopingMessage = $bobSessionCipher->encrypt($loopingMessage);

            $loopingPlaintext = $aliceSessionCipher->decryptMsg(new WhisperMessage(null, null, null, null, null, null, null, null, $bobLoopingMessage->serialize()));
            $this->assertEquals($loopingPlaintext, $loopingMessage);
        }
        foreach ($aliceOutOfOrderMessages as $aliceOutOfOrderMessage) {
            $outOfOrderPlaintext = $bobSessionCipher->decryptMsg(new WhisperMessage(null, null, null, null, null, null, null, null, $aliceOutOfOrderMessage[1]->serialize()));
            $this->assertEquals($outOfOrderPlaintext, $aliceOutOfOrderMessage[0]);
        }
    }
}
