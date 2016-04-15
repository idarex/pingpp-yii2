<?php

use idarex\pingppyii2\PingppComponent;

class PingppComponentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get chId for refunds and others.
     */
    public static function setUpBeforeClass()
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
