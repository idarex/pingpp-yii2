<?php

use idarex\pingppyii2\Channel;
use idarex\pingppyii2\TransferForm;

class TransferFormTest extends TestCase
{
    public function testValidation()
    {
        $form = new TransferForm();
        $expects = [
            'channel' => [
                [Channel::ALIPAY, 'channel can not be alipay', false],
                [Channel::WX, 'channel can be wx', true],
                [Channel::WX_PUB, 'channel can be wx_pub', true],
            ],
            'order_no' => [
                ['string', 'order_no can not be string', false],
                [str_pad('', 51, 1), 'order_no length can not great than 50', false],
                ['', 'order_no can not be empty', false],
                ['111', 'order_no is ok', true],
                [str_pad('', 50, 1), 'order_no is ok', true],
            ],
            'amount' => [
                ['string', 'amount must be number', false],
                [100, 'amount is ok', true],
                [20000, 'amount is ok', true],
            ],
            'type' => [
                ['b2b', 'type does not supported b2b', false],
                ['b2c', 'type supported b2c', true],
                ['', 'type can not be empty', false],
            ],
            'currency' => [
                ['USD', 'currency can not be USD', false],
                ['cny', 'currency could be cny', true],
            ],
            'recipient' => [
                ['', 'recipient can not be empty', false],
                ['userWechatOpenId', 'recipient is ok', true],
            ],
            'description' => [
                ['', 'description can not be empty', false],
                [str_pad('', 256, 'a'), 'description length must under 256', false],
                [str_pad('', 255, 'a'), 'description is ok', true],
            ],
        ];

        foreach ($expects as $field => $expect) {
            $fieldClosure = $this->getAssertValidate($form, $field);

            $this->expectValidate($expect, $fieldClosure);
            unset($fieldClosure);
        }
    }

    /**
     * @depends testValidation
     */
    public function testCreate()
    {
        $form = new TransferForm();
        $form->load(require 'data/transfer.php', '');
        $this->assertTrue($form->validate() && $form->create(), 'transfer created');

        $expectKeys = [
            "id",
            "object",
            "type",
            "created",
            "time_transferred",
            "livemode",
            "status",
            "app",
            "channel",
            "order_no",
            "amount",
            "currency",
            "recipient",
            "description",
            "transaction_no",
            "extra",
        ];

        $this->assertArrayHasKeys($expectKeys, $form->getData(true));
    }
}
