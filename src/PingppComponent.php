<?php

namespace idarex\pingppyii2;

use Yii;
use yii\base\Component;
use Pingpp\Pingpp;

class PingppComponent extends Component
{
    public $apiKey;
    public $appId;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->apiKey === null) {
            throw new InvalidConfigException('The apiKey property must be set.');
        }
        if ($this->appId === null) {
            throw new InvalidConfigException('The appId property must be set.');
        }
        Pingpp::setApiKey($this->apiKey);
    }
}
