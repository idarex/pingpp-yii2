<?php

use idarex\pingppyii2\PingppComponent;

class PingppComponentTest extends TestCase
{
    /**
     * Get chId for refunds and others.
     */
    public static function setUpBeforeClass()
    {
        static::createCharge();
        static::createRedEnvelop();
    }

    protected static function createCharge()
    {
        $chargeForm = new \idarex\pingppyii2\ChargeForm();
        $chargeForm->load(require 'data/charge.php', '');
        $chargeForm->create();

        Yii::$app->params['refunds.chId']
            = Yii::$app->params['retrieve.chId']
            = $chId
            = $chargeForm->getCharge()->id;

        /* @see https://github.com/PingPlusPlus/pingpp-php/issues/24 */
        file_get_contents("https://api.pingxx.com/notify/charges/{$chId}?livemode=false");
    }

    protected static function createRedEnvelop()
    {
        $f = new \idarex\pingppyii2\RedEnvelopeForm();
        $formData = require 'data/red-envelope.php';
        $f->load($formData, '');
        $f->validate() && $f->create();
        $data = $f->getData(true);
        Yii::$app->params['redEnvelope.retrieve.redId'] = $data['id'];
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testInvalidException()
    {
        Yii::createObject([
            'class' => PingppComponent::className(),
        ]);
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testInvalidExceptionWithEmptyAppId()
    {
        Yii::createObject([
            'class' => PingppComponent::className(),
            'apiKey' => 'sk_test_ibbTe5jLGCi5rzfH4OqPW9KC',
        ]);
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testInvalidExceptionWithEmptyApiKey()
    {
        Yii::createObject([
            'class' => PingppComponent::className(),
            'appId' => 'app_1Gqj58ynP0mHeX1q',
        ]);
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testInvalidExceptionWithInvalidPrivateKeyPath()
    {
        Yii::createObject([
            'class' => PingppComponent::className(),
            'apiKey' => 'sk_test_ibbTe5jLGCi5rzfH4OqPW9KC',
            'appId' => 'app_1Gqj58ynP0mHeX1q',
            'privateKeyPath' => 'invalid_private_key.pem',
        ]);
    }


    public function testRefunds()
    {
        $amount = 1;
        $desc = 'idarex pingpp-yii tests Refund Description';
        $data = Yii::$app->pingpp->refunds(Yii::$app->params['refunds.chId'], $amount, $desc);

        /* @var $refund \idarex\pingppyii2\CodeAutoCompletion\Refund */
        $refund = $data->__toStdObject();
        Yii::$app->params['refunds.reId'] = $refund->id;

        $this->compareDocs($data, 'Refund');
    }

    public function testRetrieve()
    {
        $data = Yii::$app->pingpp->retrieve(Yii::$app->params['retrieve.chId']);
        $this->compareDocs($data, 'Retrieve');
    }

    public function testChargeList()
    {
        $params = [
            'limit' => 1,
        ];

        $list = Yii::$app->pingpp->chargeList($params);
        $this->compareDocs($list, 'ListObj');
        if (isset($list->data[0])) {
            $this->compareDocs($list->data[0], 'Charge');
        }
    }

    public function testRefundRetrieve()
    {
        $data = Yii::$app->pingpp->refundRetrieve(
            Yii::$app->params['retrieve.chId'],
            Yii::$app->params['refunds.reId']
        );
        $this->compareDocs($data, 'Refund');
    }

    public function testRefundRetrieveList()
    {
        $list = Yii::$app->pingpp->refundRetrieveList(
            Yii::$app->params['retrieve.chId']
        );
        $this->compareDocs($list, 'ListObj');
        if (isset($list->data[0])) {
            $this->compareDocs($list->data[0], 'Refund');
        }
    }

    public function testRedEnvelopeRetrieve()
    {
        $data = Yii::$app->pingpp->redEnvelopeRetrieve(Yii::$app->params['redEnvelope.retrieve.redId']);
        $expectKeys = [
            "id",
            "object",
            "created",
            "received",
            "refunded",
            "livemode",
            "status",
            "app",
            "channel",
            "order_no",
            "transaction_no",
            "amount",
            "amount_settle",
            "currency",
            "recipient",
            "subject",
            "body",
            "description",
            "failure_msg",
            "extra",
        ];

        $this->assertArrayHasKeys($expectKeys, $data);
    }

    public function testRedEnvelopeList()
    {
        $params = ['limit' => 1];
        $list = Yii::$app->pingpp->redEnvelopeList($params);
        $data = $list->__toArray(true);
        $keys = ['object', 'url', 'has_more', 'data'];
        $this->assertArrayHasKeys($keys, $data);

        $subKeys = [
            'id',
            'object',
            'created',
            'received',
            'refunded',
            'livemode',
            'status',
            'app',
            'channel',
            'order_no',
            'transaction_no',
            'amount',
            'amount_settle',
            'currency',
            'recipient',
            'subject',
            'body',
            'description',
            'failure_msg',
            'extra',
            'metadata',
        ];
        if (isset($data['data'][0])) {
            $this->assertArrayHasKeys($subKeys, $data['data'][0]);
        }
    }

    public function testEventList()
    {
        $params = ['type' => 'charge.succeeded'];
        $eventList = yii::$app->pingpp->eventlist($params);
        $data = $eventList->__toarray(true);
        $keys = ['object', 'url', 'has_more', 'data'];
        $this->assertArrayHasKeys($keys, $data);

        $subKeys = [
            'id',
            'object',
            'type',
            'livemode',
            'created',
            'data',
            'pending_webhooks',
            'request',
        ];
        $this->assertArrayHasKey(0, $data['data']);
        $this->assertArrayHasKeys($subKeys, $data['data'][0]);

        return $data['data'][0];
    }

    /**
     * @depends testEventList
     */
    public function testEventRetrieve($eventData)
    {
        $this->assertArrayHasKey('id', $eventData);
        $event = Yii::$app->pingpp->eventRetrieve($eventData['id']);
        $data = $event->__toarray(true);
        $keys = ['id', 'object', 'type', 'livemode', 'created', 'pending_webhooks', 'request', 'data'];
        $this->assertArrayHasKeys($keys, $data);
    }


    public function testTransferList()
    {
        $params = ['limit' => 1];
        $list = yii::$app->pingpp->transferList($params);
        $data = $list->__toarray(true);
        $keys = ['object', 'url', 'has_more', 'data'];
        $this->assertArrayHasKeys($keys, $data);

        $subKeys = [
            'id',
            'object',
            'type',
            'livemode',
            'created',
            'time_transferred',
            'status',
            'channel',
            'order_no',
            'batch_no',
            'amount',
            'amount_settle',
            'currency',
            'recipient',
            'description',
            'transaction_no',
            'failure_msg'
        ];
        $this->assertArrayHasKey(0, $data['data']);
        $this->assertArrayHasKeys($subKeys, $data['data'][0]);

        return $data['data'][0];
    }

    /**
     * @depends testTransferList
     * @param array $transfer
     */
    public function testTransferRetrieve($transfer)
    {
        $this->assertArrayHasKey('id', $transfer);
        $event = Yii::$app->pingpp->transferRetrieve($transfer['id']);
        $data = $event->__toarray(true);
        $keys = [
            'id',
            'object',
            'type',
            'livemode',
            'created',
            'time_transferred',
            'status',
            'channel',
            'order_no',
            'batch_no',
            'amount',
            'amount_settle',
            'currency',
            'recipient',
            'description',
            'transaction_no',
            'failure_msg'
        ];
        $this->assertArrayHasKeys($keys, $data);
    }

    protected function compareDocs($rawData, $class = '')
    {
        $reflectionClass = new ReflectionClass('\idarex\pingppyii2\CodeAutoCompletion\\' . $class);
        $properties = $reflectionClass->getDefaultProperties();
        $data = is_object($rawData) ? $rawData->__toArray() : $rawData;

        $this->assertEquals(
            [],
            array_diff_key($data, $properties),
            "compare docs with class: " . $reflectionClass->name
        );
    }
}
