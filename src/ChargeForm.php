<?php

namespace idarex\pingppyii2;

use yii\base\InvalidConfigException;
use yii\base\Model;
use Yii;

class ChargeForm extends Model
{
    public $component = 'pingpp';

    public $order_no;
    /**
     * @var integer 订单总金额
     *
     * 单位为对应币种的最小货币单位
     *
     * @example 人民币为分（如订单总金额为 1 元，此处请填 100）。
     */
    public $amount;
    public $app_id;
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
     * @var array 额外参数
     * 特定渠道发起交易时需要的额外参数以及部分渠道支付成功返回的额外参数。
     */
    public $extra = [];

    /**
     * @var integer 订单失效时间
     *
     * 用 Unix 时间戳表示。时间范围在订单创建后的 1 分钟到 15 天，默认为 1 天，
     * 创建时间以 Ping++ 服务器时间为准。
     * 微信对该参数的有效值限制为 2 小时内；银联对该参数的有效值限制为 1 小时内。
     */
    public $time_expire;

    /**
     * @var array Metadata 元数据。
     *
     * @see https://pingxx.com/document/api#api-metadata
     */
    public $metadata;
    /**
     * @var string 订单附加说明，最多 255 个 Unicode 字符
     */
    public $description;

    public function rules()
    {
        return [
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
            [['client_ip', 'extra', 'time_expire', 'metadata', 'description'], 'safe'],
        ];
    }

    /**
     * @return bool|\Pingpp\Charge|CodeAutoCompletion\Charge
     * @throws \Exception
     */
    public function create()
    {
        if ($this->validate()) {
            $data = \Pingpp\Charge::create([
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

            return $data;
        }

        return false;
    }

    private $componentInstance;

    /**
     * @return PingppComponent|null|object|string
     * @throws InvalidConfigException
     */
    public function getComponent()
    {
        if ($this->componentInstance !== null) {
            return $this->componentInstance;
        }

        if (is_string($this->component)) {
            return $this->componentInstance = Yii::$app->get($this->component);
        } elseif ($this->component instanceof PingppComponent) {
            return $this->componentInstance = $this->component;
        }
        throw new InvalidConfigException('ping plus plus component config error.');
    }
}
