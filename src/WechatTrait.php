<?php

namespace idarex\pingppyii2;

use Pingpp\Charge;
use Pingpp\WxpubOAuth;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;

trait WechatTrait
{
    public $wxAppId;
    public $wxAppSecret;

    public $cacheTime = 7000;
    protected $cachePrefix = 'wechatCache_';
    protected $jsApiTicketCacheKey = 'jsApiTicket';

    /**
     * 获取 JsApiTicket
     *
     * @see WechatTrait::$cacheTime
     * @see WechatTrait::$cachePrefix
     * @see WechatTrait::$jsApiTicketCacheKey
     *
     * @param bool $force
     * @return string
     * @throws InvalidConfigException
     */
    public function getJsApiTicket($force = false)
    {
        $this->checkWechatConfig();
        if ($this->_jsApiTicket === null || $this->_jsApiTicket['expire'] < YII_BEGIN_TIME || $force) {
            $result = false;
            if (!$force && $this->_jsApiTicket === null) {
                $result = $this->getCache($this->jsApiTicketCacheKey, false);
            }

            if ($result === false) {
                $result = WxpubOAuth::getJsapiTicket($this->wxAppId, $this->wxAppSecret);
                if (!$result) {
                    throw new \BadMethodCallException('get wechat jsApiTicket error.');
                }
                if ($result['errcode'] != 0) {
                    throw new \BadMethodCallException('get wechat jsApiTicket error: ' . $result['errmsg']);
                }
                $this->setCache($this->jsApiTicketCacheKey, $result, $result['expires_in']);
            }

            $result['expire'] = YII_BEGIN_TIME + $result['expires_in'];
            $this->setJsApiTicket($result);
        }

        return $this->_jsApiTicket['ticket'];
    }

    public function getSignature(Charge $charge, $ticket, $url = null)
    {
        return WxpubOAuth::getSignature($charge, $ticket, $url);
    }

    protected function checkWechatConfig()
    {
        if ($this->wxAppId === null) {
            throw new InvalidConfigException('The wxAppId property must be set.');
        }
        if ($this->wxAppSecret === null) {
            throw new InvalidConfigException('The wxAppSecret property must be set.');
        }
    }

    private $_jsApiTicket;

    protected function setJsApiTicket(array $data)
    {
        if (!isset($data['ticket'])) {
            throw new InvalidParamException('The wechat jsApiTicket must be set.');
        } elseif (!isset($data['expire'])) {
            throw new InvalidParamException('Wechat jsApiTicketExpire time must be set.');
        }
        $this->_jsApiTicket = [
            'ticket' => $data['ticket'],
            'expire' => $data['expire']
        ];
    }

    protected function setCache($name, $value, $duration = null)
    {
        $duration === null && $duration = $this->cacheTime;

        return Yii::$app->getCache()->set($this->getCacheKey($name), $value, $duration);
    }

    protected function getCache($name, $defaultValue = null)
    {
        return Yii::$app->getCache()->get($this->getCacheKey($name), $defaultValue);
    }

    protected function getCacheKey($name)
    {
        return implode('_', [$this->cachePrefix, $this->wxAppId, $name]);
    }
}
