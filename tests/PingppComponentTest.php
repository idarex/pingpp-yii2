<?php

use idarex\pingppyii2\PingppComponent;
use idarex\pingppyii2\ChargeEvent;
use idarex\pingppyii2\Channel;
use idarex\pingppyii2\Charge;

class PingppComponentTest extends PHPUnit_Framework_TestCase
{
    public $chargeData = [
        'order_no' => '123',
        'channel' => Channel::WX,
        'amount' => '1',
    ];

    /**
     * @var PingppComponent
     */
    private $_component;

    public function setUp()
    {
        $this->_component = Yii::createObject([
            'class' => PingppComponent::className(),
            'apiKey' => 'sk_test_ibbTe5jLGCi5rzfH4OqPW9KC',
            'appId' => 'app_1Gqj58ynP0mHeX1q',
        ]);
    }

    public function testCharge()
    {
        $charge = new Charge;
        $charge->component = $this->_component;
        $charge->load($this->chargeData);
        if ($charge->validate() && $charge->create()) {
            echo 'ok';
        }
    }
}
