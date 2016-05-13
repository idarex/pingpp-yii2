<?php

use idarex\pingppyii2\RedEnvelopeForm;
use idarex\pingppyii2\Channel;

use Cypress\Curry as C;

class RedEnvelopeFormTests extends TestCase
{
    public function testOrderNo()
    {
        $f = new RedEnvelopeForm();
        $expects = [
            'order_no' => [
                ['aas2', 'order_no must be numbers', false],
                ['aas2', 'order_no must be numbers', false],
                [str_pad('', 29, '1'), 'order_no length must less then 28', false],

                [str_pad('', 24, '1'), 'order_no length 24 is ok', true],
                [str_pad('', 3, '1'), 'order_no length 3 is ok', true],
            ],
            'channel' => [
                [Channel::ALIPAY, 'channel must range wx and wx_pub', false],
                [Channel::WX, 'channel must range wx and wx_pub', true],
                [Channel::WX_PUB, 'channel must range wx and wx_pub', true],
            ],
            'amount' => [
                [1, 'amount can not less than 100', false],
                [30000, 'amount can not great than 20000', false],

                [100, '', true],
                [20000, '', true],
            ],
            'currency' => [
                ['USD', 'currency can not be USD', false],
                ['cny', 'currency must be cny', true],
            ],
        ];
        foreach ($expects as $field => $expect) {
            $fieldClosure = $this->getAssertValidate($f, $field);

            $this->expectValidate($expect, $fieldClosure);
            unset($fieldClosure);
        }
    }

    public function testCreate()
    {
        sleep(61);
        $f = new RedEnvelopeForm();
        $formData = require 'data/red-envelope.php';
        $f->load($formData, '');
        $this->assertTrue($f->validate() && $f->create());
        $data = $f->getData(true);

        $keys = [
            'id',
            'object',
            'order_no',
        ];
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $data);
        }

        $this->assertEquals($formData['order_no'], $data['order_no']);
    }
}
