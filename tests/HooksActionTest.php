<?php

use idarex\pingppyii2\PingppComponent;
use yii\base\InvalidConfigException;
use idarex\pingppyii2\HooksAction;

class HooksActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testInvalidExceptionWithEmptyPingHooksComponentClass()
    {
        $controller = new \yii\base\Controller('tests', 'tests');
        $id = 'pingpp-hooks';

        Yii::createObject([ 'class' => HooksAction::className() ], [$id, $controller, [
            'publicKeyPath' => __DIR__ . '/rsa_public_key.pem',
        ]]);
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testInvalidExceptionWithEmptyPublicKeyPath()
    {
        $controller = new \yii\base\Controller('tests', 'tests');
        $id = 'pingpp-hooks';

        Yii::createObject([ 'class' => HooksAction::className() ], [$id, $controller, [
            'pingppHooksComponentClass' => \PingppHooksComponent::className(),
        ]]);
    }

    public function testSuccessful()
    {
        $controller = new \yii\base\Controller('tests', 'tests');
        $id = 'pingpp-hooks';

        $action = Yii::createObject([ 'class' => HooksAction::className() ], [$id, $controller, [
            'pingppHooksComponentClass' => \PingppHooksComponent::className(),
            'publicKeyPath' => __DIR__ . '/rsa_public_key.pem',
        ]]);

        $this->assertTrue($action instanceof \yii\base\Action);
    }
}
