<?php

namespace idarex\pingppyii2;

use Pingpp\Collection;
use Pingpp\Event;
use Pingpp\RedEnvelope;
use Pingpp\Transfer;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use Pingpp\Pingpp;
use Pingpp\Charge;

/**
 * Class PingppComponent
 * @package idarex\pingppyii2
 *
 * @property-write string $publicKeyPath 请求签名公钥文件的路径
 * @property string $publicKey 请求签名公钥内容
 */
class PingppComponent extends Component
{
    use WechatTrait;

    public $apiKey;
    public $appId;

    /**
     * 请求签名私钥文件的路径
     * 支持路径别名
     *
     * @see Yii::getAlias()
     * @var string
     */
    public $privateKeyPath;

    /**
     * 设置私钥内容
     *
     * @var string
     */
    public $privateKey;

    private $_publicKey;

    /**
     * @param string $path
     * @throws InvalidConfigException
     */
    public function setPublicKeyPath($path)
    {
        if (!$path || !file_exists($fullPath = Yii::getAlias($path))) {
            throw new InvalidConfigException('The public key file not exists.');
        }
        $this->_publicKey = file_get_contents($fullPath);
    }

    public function getPublicKey()
    {
        return $this->_publicKey;
    }

    /**
     * @param string $content
     */
    public function setPublicKey($content)
    {
        $this->_publicKey = $content;
    }

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

        if (!empty($this->privateKeyPath)) {
            $privateKeyFullPath = Yii::getAlias($this->privateKeyPath);
            if (!file_exists($privateKeyFullPath)) {
                throw new InvalidConfigException('The private key file not exists.');
            }

            Pingpp::setPrivateKeyPath($privateKeyFullPath);
        } elseif (!empty($this->privateKey)) {
            Pingpp::setPrivateKey($this->privateKey);
        }
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
     * @return array|Collection
     */
    public function redEnvelopeList($params = [], $options = [])
    {
        return RedEnvelope::all($params, $options);
    }

    /**
     * 获取指定事件
     *
     * @param $eventId
     * @param array $options
     * @return Event
     */
    public function eventRetrieve($eventId, $options = [])
    {
        return Event::retrieve($eventId, $options);
    }

    /**
     * 获取事件列表
     *
     * @param array $params
     * @param array $options
     * @return array|Collection
     */
    public function eventList($params = [], $options = [])
    {
        return Event::all($params, $options);
    }

    /**
     * @param $chId
     * @return Collection
     */
    protected function getRefunds($chId)
    {
        return Charge::retrieve($chId)->refunds;
    }

    /**
     * 查询 Transfer 列表
     *
     * @param array $params
     * @param array $options
     *
     * @return Transfer[]
     */
    public function transferList($params = [], $options = [])
    {
        return Transfer::all($params, $options);
    }

    /**
     * 查询指定 transfer 对象
     *
     * @param string $transferId The ID of the transfer to retrieve.
     * @param array|string|null $options
     * @return Transfer
     */
    public function transferRetrieve($transferId, $options = null)
    {
        return Transfer::retrieve($transferId, $options);
    }
}
