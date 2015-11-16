<?php

namespace idarex\pingppyii2;

use Yii;
use yii\base\Component;
use Pingpp\Pingpp;

class PingppComponent extends Component
{
    public $apiKey;

    public function init()
    {
        if ($this->apiKey === null) {
            throw new InvalidConfigException('The apiKey property must be set.');
        }
    }
}
