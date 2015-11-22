<?php

namespace idarex\pingppyii2;

use yii\base\Component;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * Class HooksRoute
 * @package idarex\pingppyii2
 */
class Hooks extends Component
{
    /**
     * @var PingppComponent|string the PingppComponent object or the ID of the pingpp application component
     */
    public $pingpp = 'pingpp';

    private $_publicKey;

    public function getPublicKey()
    {
        return $this->_publicKey;
    }

    public function setPublicKey($publicKey)
    {
        $this->_publicKey = $publicKey;
    }

    public function setPublicKeyPath($path)
    {
        $file = Yii::getAlias($path);
        if (file_exists($file) && $content = file_get_contents($file)) {
            $this->_publicKey = $content;
        } else {
            throw new InvalidConfigException('The publicKeyPath must be a file as pem.');
        }
    }

    public function verify($rawData)
    {
        $headers = Yii::$app->request->getHeaders();
        $signature = $headers->get('x-pingplusplus-signature');

        return self::verifySign($rawData, $signature, $this->_publicKey);
    }

    public static function verifySign($rawData, $signature, $publicKey)
    {
        return openssl_verify($rawData, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }

    protected $event;

    public function run()
    {
        $rawData = Yii::$app->getRequest()->getRawBody();
        if (!$this->verify($rawData)) {
            throw new ForbiddenHttpException;
        }

        $event = Json::decode($rawData, false);
        if (!isset($event->type)) {
            throw new BadRequestHttpException;
        }
        $this->event = $event;

        switch ($event->type) {
            case HooksInterface::SUMMARY_DAILY_AVAILABLE:
                $this->onAvailableDailySummary();
                break;
            case HooksInterface::SUMMARY_WEEKLY_AVAILABLE:
                $this->onAvailableWeeklySummary();
                break;
            case HooksInterface::SUMMARY_MONTHLY_AVAILABLE:
                $this->onAvailableMonthlySummary();
                break;
            case HooksInterface::CHARGE_SUCCEEDED:
                $this->onSucceededCharge();
                break;
            case HooksInterface::REFUND_SUCCEEDED:
                $this->onSucceededRefund();
                break;
            case HooksInterface::TRANSFER_SUCCEEDED:
                $this->onSucceededTransfer();
                break;
            case HooksInterface::RED_ENVELOPE_SENT:
                $this->onSentRedEnvelope();
                break;
            case HooksInterface::RED_ENVELOPE_RECEIVED:
                $this->onReceivedRedEnvelope();
                break;
            default:
                throw new BadRequestHttpException;
                break;
        }
    }

    public function getComponent()
    {
        if (is_string($this->pingpp) && Yii::$app) {
            $component = Yii::$app->get($this->queryCache, false);
        } else {
            $component = $this->pingpp;
        }

        if ($component instanceof PingppComponent) {
            return $component;
        }
    }
}
