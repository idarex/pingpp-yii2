<?php

namespace idarex\pingppyii2;

use Pingpp\Charge;
use Pingpp\WxpubOAuth;
use yii\base\InvalidConfigException;
use Yii;
use yii\helpers\Url;

class ChargeForm extends Model
{
    public $order_no;
    /**
     * @var integer 订单总金额
     *
     * 单位为对应币种的最小货币单位
     *
     * @example 人民币为分（如订单总金额为 1 元，此处请填 100）。
     */
    public $amount;
    public $channel;
    /**
     * @var string 三位 ISO 货币代码
     *
     * 目前仅支持人民币 cny。
     */
    public $currency;
    public $client_ip;
    /**
     * @var string 商品的标题
     *
     * 该参数最长为 32 个 Unicode 字符，银联全渠道（upacp/upacp_wap）限制在 32 个字节。
     */
    public $subject;
    /**
     * @var string 商品的描述信息
     *
     * 该参数最长为 128 个 Unicode 字符，yeepay_wap 对于该参数长度限制为 100 个 Unicode 字符。
     */
    public $body;

    /**
     * @var integer 订单失效时间
     *
     * 用 Unix 时间戳表示。时间范围在订单创建后的 1 分钟到 15 天，默认为 1 天，
     * 创建时间以 Ping++ 服务器时间为准。
     * 微信对该参数的有效值限制为 2 小时内；银联对该参数的有效值限制为 1 小时内。
     */
    public $time_expire;

    /**
     * @var string 订单附加说明，最多 255 个 Unicode 字符
     */
    public $description;

    public function rules()
    {
        return array_merge([
            [['order_no', 'amount', 'channel', 'currency', 'subject', 'body'], 'required'],
            ['amount', 'number', 'min' => 1],
            ['channel', 'in', 'range' => Channel::$allChannel],
            [
                'currency',
                function ($attribute) {
                    if ($this->$attribute != 'cny') {
                        $this->addError($attribute, "The currency must be 'cny'");
                    }
                }
            ],
            [['client_ip', 'time_expire', 'description'], 'safe'],
        ], parent::rules());
    }

    /**
     * @var Charge
     */
    private $_charge;

    /**
     * @return bool|\Pingpp\Charge|CodeAutoCompletion\Charge
     * @throws \Exception
     */
    public function create()
    {
        if ($this->validate()) {
            $this->_charge = \Pingpp\Charge::create([
                'order_no' => $this->order_no,
                'amount' => $this->amount,
                'app' => [
                    'id' => $this->getComponent()->appId,
                ],
                'channel' => $this->channel,
                'currency' => $this->currency,
                'client_ip' => $this->client_ip,
                'subject' => $this->subject,
                'body' => $this->body,
                'extra' => $this->extra,
            ]);

            return true;
        }

        return false;
    }

    /**
     * @param bool $asArray
     * @return array|\idarex\pingppyii2\CodeAutoCompletion\Charge
     */
    public function getCharge($asArray = false)
    {
        if ($asArray) {
            return $this->_charge->__toArray(true);
        } else {
            return $this->_charge->__toStdObject();
        }
    }

    /**
     * 获取微信支付时的签名
     *
     * @param null|array $charge
     * @param null|string $url
     * @return string
     * @throws InvalidConfigException
     */
    public function getWechatSignature($charge = null, $url = null)
    {
        if ($charge === null) {
            $charge = $this->getCharge(true);
        }

        if (!isset($charge['credential']) || !isset($charge['credential']['wx_pub'])) {
            throw new \BadMethodCallException('Credential must be ' . Channel::WX_PUB);
        }

        $component = $this->getComponent();
        $jsApiTicket = $component->getJsApiTicket();
        if ($url === null) {
            $url = Url::current([], true);
        }

        return WxpubOAuth::getSignature($this->getCharge(true), $jsApiTicket, $url);
    }
}
