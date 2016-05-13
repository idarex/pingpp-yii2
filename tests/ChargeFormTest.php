<?php

use idarex\pingppyii2\ChargeForm;

class ChargeFormTest extends TestCase
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

    /**
     * @depends testCreate
     */
    public function testCreateWithPrivateKeyPath()
    {
        $config = Yii::$app->getComponents()['pingpp'];
        $config['privateKeyPath'] = dirname(__FILE__) . '/rsa_private_key.pem';
        $component = Yii::createObject($config);

        $chargeForm = new ChargeForm();
        $chargeForm->component = $component;
        $chargeForm->load(require 'data/charge.php', '');

        $this->assertTrue($chargeForm->validate() && $chargeForm->create());
        $this->assertTrue(is_array($chargeForm->getCharge(true)));
    }

    /**
     * @depends testCreate
     */
    public function testCreateWithPrivateKey()
    {
        $config = Yii::$app->getComponents()['pingpp'];
        $config['privateKey'] = file_get_contents(dirname(__FILE__) . '/rsa_private_key.pem');

        $component = Yii::createObject($config);

        $chargeForm = new ChargeForm();
        $chargeForm->component = $component;
        $chargeForm->load(require 'data/charge.php', '');

        $this->assertTrue($chargeForm->validate() && $chargeForm->create());
        $this->assertTrue(is_array($chargeForm->getCharge(true)));
    }
}
