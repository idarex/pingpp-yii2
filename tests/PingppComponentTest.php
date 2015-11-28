<?php

use idarex\pingppyii2\PingppComponent;
use idarex\pingppyii2\ChargeForm;

class PingppComponentTest extends PHPUnit_Framework_TestCase
{
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
        $this->compareDocs($data, 'Refunds');
    }

    public function testRetrieve()
    {
        $data = Yii::$app->pingpp->retrieve(Yii::$app->params['retrieve.chId']);
        $this->compareDocs($data, 'Retrieve');
    }

    public function testChargeList()
    {
        $options = [
            'limit' => 1,
        ];

        $data = Yii::$app->pingpp->chargeList($options);
        if (!empty($data)) {
            $data = array_pop($data);
        }
        $this->compareDocs($data, 'Charge');
    }

    protected function compareDocs($rawData, $class = '')
    {
        $reflectionClass = new ReflectionClass('\idarex\pingppyii2\CodeAutoCompletion\\' . $class);
        $properties = $reflectionClass->getDefaultProperties();
        $data = is_object($rawData) ? $rawData->__toArray() : $rawData;

        $this->assertTrue(array_diff_key($data, $properties) == []);
    }
}
