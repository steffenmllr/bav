<?php

namespace Bav\Validator\De;

/**
 * Test class for System21.
 * Generated by PHPUnit on 2012-07-05 at 19:22:55.
 */
class System21Test extends \PHPUnit_Framework_TestCase
{

    public function testWithValidAccountReturnsTrue()
    {
        $validAccounts = array('200520013', '30052004');

        foreach ($validAccounts as $account) {
            $validator = new System21();
            $this->assertTrue($validator->isValid($account));
        }
    }

    public function testWithInvalidAccountReturnsFalse()
    {
        $validAccounts = array('1000805', '539290859');

        foreach ($validAccounts as $account) {
            $validator = new System21();
            $this->assertFalse($validator->isValid($account));
        }
    }

}