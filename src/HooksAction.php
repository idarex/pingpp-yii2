<?php

namespace idarex\pingppyii2;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Action;

class HooksAction extends Action
{
    public $publicKeyPath;
    public $pingppHooksComponentClass;

    public function init()
    {
        if ($this->publicKeyPath === null) {
            throw new InvalidConfigException("publicKeyPath MUST set.");
        }

        if ($this->pingppHooksComponentClass === null) {
            throw new InvalidConfigException("pingppHooksComponentClass MUST set.");
        }

        parent::init();
    }

    public function run()
    {
        /* @var Hooks $hooks */
        $hooks = Yii::createObject([
            'class' => $this->pingppHooksComponentClass,
            'publicKeyPath' => $this->publicKeyPath,
        ]);
        $hooks->run();
    }
}
