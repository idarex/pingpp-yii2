<?php

namespace idarex\pingppyii2;

use yii\base\InvalidConfigException;
use Yii;

class Model extends \yii\base\Model
{
    public $component = 'pingpp';

    /**
     * @var array 额外参数
     * 特定渠道发起交易时需要的额外参数以及部分渠道支付成功返回的额外参数。
     */
    public $extra = [];

    /**
     * @var array Metadata 元数据。
     *
     * @see https://pingxx.com/document/api#api-metadata
     */
    public $metadata = [];

    public function rules()
    {
        return [
            [['metadata', 'extra'], 'safe'],
        ];
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
