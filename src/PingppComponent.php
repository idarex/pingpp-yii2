<?php

namespace idarex\pingppyii2;

use Pingpp\RedEnvelope;
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
     * @param array $params
     * @param array $options
     * @return CodeAutoCompletion\ListObj
     */
    public function chargeList($params = [], $options = [])
    {
        return Charge::all($params, $options);
    }

    /**
     * 查询单笔退款
     *
     * @param $chId
     * @param $refundId
     * @param array $params
     * @param array $opts
     * @return CodeAutoCompletion\Refund
     */
    public function refundRetrieve($chId, $refundId, $params = null, $opts = null)
    {
        $refunds = $this->getRefunds($chId);

        return $refunds->retrieve($refundId, $params, $opts);
    }

    /**
     * 查询退款列表
     *
     * @param string $chId
     * @param array $params
     * @param array $opts
     * @return CodeAutoCompletion\ListObj
     */
    public function refundRetrieveList($chId, $params = null, $opts = null)
    {
        $refunds = $this->getRefunds($chId);

        return $refunds->all($params, $opts);
    }

    /**
     * 查询指定微信红包
     *
     * @param $redId
     * @param null|array|string $options
     * @return RedEnvelope
     */
    public function redEnvelopeRetrieve($redId, $options = null)
    {
        return RedEnvelope::retrieve($redId, $options);
    }

    /**
     * 查询微信红包列表
     *
     * @param array $params
     * @param array $options
     * @return array|\Pingpp\Collection
     */
    public function redEnvelopeList($params = [], $options = [])
    {
        return RedEnvelope::all($params, $options);
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
