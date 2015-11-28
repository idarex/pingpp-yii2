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
     * @return CodeAutoCompletion\Refund
     */
    public function refunds($chId, $amount, $description)
    {
        $refunds = $this->getRefunds($chId);
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
     * @return CodeAutoCompletion\Retrieve
     */
    public function retrieve($chId)
    {
        return Charge::retrieve($chId);
    }

    /**
     * 查询 Charge 对象列表
     * @param array $options
     * @return CodeAutoCompletion\ListObj
     */
    public function chargeList($options = [])
    {
        return Charge::all($options);
    }

    /**
     * @param $chId
     * @return \Pingpp\Collection
     */
    protected function getRefunds($chId)
    {
        return Charge::retrieve($chId)->refunds;
    }
}
