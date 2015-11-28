<?php

use idarex\pingppyii2\Hooks;
use idarex\pingppyii2\HooksInterface;

class PingppHooksComponent extends Hooks implements HooksInterface
{

    /**
     * @inheritdoc
     */
    public function onAvailableDailySummary()
    {
    }

    /**
     * @inheritdoc
     */
    public function onAvailableWeeklySummary()
    {
    }

    /**
     * @inheritdoc
     */
    public function onAvailableMonthlySummary()
    {
    }

    /**
     * @inheritdoc
     */
    public function onSucceededCharge()
    {
    }

    /**
     * @inheritdoc
     */
    public function onSucceededRefund()
    {
    }

    /**
     * @inheritdoc
     */
    public function onSucceededTransfer()
    {
    }

    /**
     * @inheritdoc
     */
    public function onSentRedEnvelope()
    {
    }

    /**
     * @inheritdoc
     */
    public function onReceivedRedEnvelope()
    {
    }
}
