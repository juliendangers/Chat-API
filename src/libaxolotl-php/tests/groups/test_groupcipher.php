<?php

namespace LibAxolotl\Tests\Groups;

use LibAxolotl\Groups\GroupSessionBuilder;
use LibAxolotl\Groups\GroupCipher;
use LibAxolotl\Utils\KeyHelper;

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
class GroupCipherTest extends \PHPUnit_Framework_TestCase
{
    public function test_basicEncryptDecrypt()
    {
        $aliceStore = new InMemorySenderKeyStore();
        $bobStore = new InMemorySenderKeyStore();
        $charlieStore = new InMemorySenderKeyStore();

        $aliceSessionBuilder = new GroupSessionBuilder($aliceStore);
        $bobSessionBuilder = new GroupSessionBuilder($bobStore);
        $charlieSessionBuilder = new GroupSessionBuilder($charlieStore);

        $aliceGroupCipher = new GroupCipher($aliceStore, 'groupWithBobInIt');
        $bobGroupCipher = new GroupCipher($bobStore, 'groupWithBobInIt::aliceUserName');
        $charlieGroupCipher = new GroupCipher($charlieStore, 'groupWithBobInIt::aliceUserName');

        $aliceSenderKey = KeyHelper::generateSenderKey();
        $aliceSenderSigningKey = KeyHelper::generateSenderSigningKey();
        $aliceSenderKeyId = KeyHelper::generateSenderKeyId();

        $aliceDistributionMessage = $aliceSessionBuilder->process('groupWithBobInIt', $aliceSenderKeyId, 0,
                                $aliceSenderKey, $aliceSenderSigningKey);
        echo niceVarDump($aliceDistributionMessage);
        echo niceVarDump($aliceDistributionMessage->serialize());
        echo $aliceDistributionMessage->serialize();
        $bobSessionBuilder->processSender('groupWithBobInIt::aliceUserName', $aliceDistributionMessage);

        $ciphertextFromAlice = $aliceGroupCipher->encrypt('smert ze smert');
        $plaintextFromAlice_Bob = $bobGroupCipher->decrypt($ciphertextFromAlice);
        $ciphertextFromAlice_2 = $aliceGroupCipher->encrypt('smert ze smert');
        echo niceVarDump($aliceDistributionMessage);
        $charlieSessionBuilder->processSender('groupWithBobInIt::aliceUserName', $aliceDistributionMessage);
        $plaintextFromAlice_Charlie = $charlieGroupCipher->decrypt($ciphertextFromAlice_2);

        $this->assertEquals($plaintextFromAlice_Bob, 'smert ze smert');
        $this->assertEquals($plaintextFromAlice_Charlie, 'smert ze smert');
    }
}
