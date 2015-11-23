<?php

use idarex\pingppyii2\PingppComponent;
use yii\base\InvalidConfigException;

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
}
