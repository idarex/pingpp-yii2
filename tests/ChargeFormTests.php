<?php


use idarex\pingppyii2\ChargeForm;

class ChargeFormTests extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $chargeForm = new ChargeForm();
        $chargeForm->load(require 'data/charge.php', '');
        $this->assertTrue($chargeForm->validate());

        $data = $chargeForm->create()->__toArray();
        $this->assertTrue(is_array($data));

        $reflectionClass = new ReflectionClass('\idarex\pingppyii2\CodeAutoCompletion\Charge');
        $properties = $reflectionClass->getDefaultProperties();

        $this->assertTrue(array_diff_key($data, $properties) == []);
    }
}