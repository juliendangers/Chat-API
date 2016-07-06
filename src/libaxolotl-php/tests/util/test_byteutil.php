<?php

namespace LibAxolotl\Tests\Utils;

use LibAxolotl\Utils\ByteUtil;;

class ByteUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testSplit()
    {
        $data = '';
        for ($i = 0; $i < 80; $i++) {
            $data .= chr($i);
        }
        $a_data = '';
        for ($i = 0; $i < 32; $i++) {
            $a_data .= chr($i);
        }
        $b_data = '';
        for ($i = 32; $i < 64; $i++) {
            $b_data .= chr($i);
        }
        $c_data = '';
        for ($i = 64; $i < 80; $i++) {
            $c_data .= chr($i);
        }

        $result = ByteUtil::split($data, 32, 32, 16);
        $this->assertEquals($result[0], $a_data);
        $this->assertEquals($result[1], $b_data);
        $this->assertEquals($result[2], $c_data);
    }
}
