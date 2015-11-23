<?php

use idarex\pingppyii2\Hooks;
use idarex\pingppyii2\HooksInterface;

class PingppHooksComponent extends Hooks implements HooksInterface
{

    /**
     *      * @inheritdoc
     *           */
    public function onAvailableDailySummary()
    {
        Yii::$app->end();
    }

    /**
     *      * @inheritdoc
     *           */
    public function onAvailableWeeklySummary()
    {
        Yii::$app->end();
    }

    /**
     *      * @inheritdoc
     *           */
    public function onAvailableMonthlySummary()
    {
        Yii::$app->end();
    }

    /**
     *      * @inheritdoc
     *           */
    public function onSucceededCharge()
    {
        $orderId = $this->event->data->object->order_no;
        Yii::$app->getResponse()->data = 'finished job';
        Yii::$app->end();
    }

    /**
     *      * @inheritdoc
     *           */
    public function onSucceededRefund()
    {
        Yii::$app->end();
    }

    /**
     *      * @inheritdoc
     *           */
    public function onSucceededTransfer()
    {
        Yii::$app->end();
    }

    /**
     *      * @inheritdoc
     *           */
    public function onSentRedEnvelope()
    {
        Yii::$app->end();
    }

    /**
     *      * @inheritdoc
     *           */
    public function onReceivedRedEnvelope()
    {
        Yii::$app->end();
    }
}
