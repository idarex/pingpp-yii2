<?php

namespace idarex\pingppyii2;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use Pingpp\Pingpp;
use Pingpp\Charge;

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

    /**
     * 退款
     *
     * @param string $chId
     * @param integer $amount
     * @param string $description
     * @return CodeAutoCompletion\Refunds|\Pingpp\Object
     */
    public function refunds($chId, $amount, $description)
    {
        /* @var \Pingpp\Collection $refunds */
        $refunds = Charge::retrieve($chId)->refunds;

        $data = $refunds->create([
            'amount' => $amount,
            'description' => $description,
        ]);

        return $data;
    }

    /**
     * 查询单笔交易
     *
     * @param $chId
     * @return array|Charge|CodeAutoCompletion\Retrieve
     */
    public function retrieve($chId)
    {
        return Charge::retrieve($chId);
    }
}
