<?php

use idarex\pingppyii2\ChargeForm;

class ChargeFormTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $chargeForm = new ChargeForm();
        $chargeForm->load(require 'data/charge.php', '');
        $this->assertTrue($chargeForm->validate());

        $this->assertTrue($chargeForm->create());
        $this->assertTrue(is_array($chargeForm->getCharge(true)));

        $reflectionClass = new ReflectionClass('\idarex\pingppyii2\CodeAutoCompletion\Charge');
        $properties = $reflectionClass->getDefaultProperties();

        $this->assertTrue(array_diff_key($chargeForm->getCharge(true), $properties) == []);
    }

    /**
     * @depends testCreate
     */
    public function testGetWechatSignature()
    {
        $chargeForm = new ChargeForm();
        $chargeForm->load(require 'data/wechat-charge.php', '');
        $this->assertTrue($chargeForm->validate() && $chargeForm->create(), 'Create wechat charge');

        $signature = $chargeForm->getWechatSignature(null, 'https://m.idarex.com');
        $this->assertNotEmpty($signature, 'Signature can not be empty');
    }
}
