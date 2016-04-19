<?php

namespace idarex\pingppyii2;

use Pingpp\PingppObject;
use Pingpp\RedEnvelope;
use yii\base\InvalidConfigException;
use yii\base\Model;
use Yii;

class RedEnvelopeForm extends Model
{
    public $component = 'pingpp';

    public $order_no;
    /**
     * @var integer 总金额
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
    /**
     * @var string 红包主题名称
     *
     * 该参数最长为 32 个 Unicode 字符，银联全渠道（upacp/upacp_wap）限制在 32 个字节。
     */
    public $subject;
    /**
     * @var string 红包祝福语
     *
     * 该参数最长为 128 个 Unicode 字符，yeepay_wap 对于该参数长度限制为 100 个 Unicode 字符。
     */
    public $body;

    public $recipient;

    /**
     * @var string 备注说明，最多 255 个 Unicode 字符
     */
    public $description;

    public $metadata = [];

    /**
     * @var string 提供方名称
     *
     * 最多 32 个字节
     */
    public $nickname;
    /**
     * @var string 商户名称
     *
     * 最多 32 个字节。
     */
    public $sendName;

    /**
     * @var string 商户 logo 图片的 URL。
     */
    public $logo;
    /**
     * @var string 分享链接 URL。
     */
    public $shareUrl;
    /**
     * @var string 分享链接配的文字
     *
     * 最多 255 个字节。
     */
    public $shareContent;
    /**
     * @var string 分享链接配的图片 URL。
     */
    public $shareImg;

    public function rules()
    {
        return [
            [
                ['nickname', 'sendName', 'recipient', 'order_no', 'amount', 'channel', 'currency', 'subject', 'body'],
                'required'
            ],
            ['order_no', 'number'],
            [['nickname', 'sendName'], 'string', 'max' => 32],
            [['shareContent'], 'string', 'max' => 255],
            ['order_no', 'string', 'min' => 1, 'max' => 28],
            ['amount', 'number', 'min' => 100, 'max' => 20000],
            ['channel', 'in', 'range' => [Channel::WX, Channel::WX_PUB]],
            [
                'currency',
                function ($attribute) {
                    if ($this->$attribute != 'cny') {
                        $this->addError($attribute, "The currency must be 'cny'");
                    }
                }
            ],
            [['description', 'metadata'], 'safe'],
        ];
    }

    /**
     * @var RedEnvelope
     */
    private $_response;

    /**
     * @return bool|\Pingpp\Charge|CodeAutoCompletion\Charge
     * @throws \Exception
     */
    public function create()
    {
        if ($this->validate()) {
            $extra = [
                'nick_name' => $this->nickname,
                'send_name' => $this->sendName,
            ];
            if ($this->logo !== null) {
                $extra['logo'] = $this->logo;
            }
            if ($this->shareUrl !== null) {
                $extra['share_url'] = $this->shareUrl;
            }
            if ($this->shareContent !== null) {
                $extra['share_content'] = $this->shareContent;
            }
            if ($this->shareImg !== null) {
                $extra['share_img'] = $this->shareImg;
            }
            $this->_response = RedEnvelope::create([
                'order_no' => $this->order_no,
                'app' => [
                    'id' => $this->getComponent()->appId,
                ],
                'channel' => $this->channel,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'subject' => $this->subject,
                'body' => $this->body,
                'extra' => $extra,
                'recipient' => $this->recipient,
                'description' => $this->description,
                'metadata' => $this->metadata,
            ]);

            return true;
        }

        return false;
    }

    /**
     * @param bool $asArray
     * @return array|PingppObject
     */
    public function getData($asArray = false)
    {
        if ($asArray) {
            return $this->_response->__toArray(true);
        }

        return $this->_response->__toStdObject();
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
