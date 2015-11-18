<?php

namespace idarex\pingppyii2;

/**
 * Interface HooksRouteInterface
 * @package idarex\pingppyii2
 */
interface HooksInterface
{
    /**
     * 日汇总
     *
     * @var string
     */
    const SUMMARY_DAILY_AVAILABLE = 'summary.daily.available';
    /**
     * 周汇总
     *
     * @var string
     */
    const SUMMARY_WEEKLY_AVAILABLE = 'summary.weekly.available';
    /**
     * 月汇总
     *
     * @var string
     */
    const SUMMARY_MONTHLY_AVAILABLE = 'summary.monthly.available';
    /**
     * 支付成功
     *
     * @var string
     */
    const CHARGE_SUCCEEDED = 'charge.succeeded';
    /**
     * 退款成功
     *
     * @var string
     */
    const REFUND_SUCCEEDED = 'refund.succeeded';
    /**
     * 企业付款成功
     *
     * @var string
     */
    const TRANSFER_SUCCEEDED = 'transfer.succeeded';
    /**
     * 红包发送成功
     *
     * @var string
     */
    const RED_ENVELOPE_SENT = 'red_envelope.sent';
    /**
     * 红包已领取
     *
     * @var string
     */
    const RED_ENVELOPE_RECEIVED = 'red_envelope.received';

    /**
     * 路由
     *
     * 这里需要确定请求发给哪个方法去具体地处理
     *
     * @return mixed
     */
    public function run();

    /**
     * 日汇总
     *
     * @return mixed
     */
    public function onAvailableDailySummary();

    /**
     * 周汇总
     *
     * @return mixed
     */
    public function onAvailableWeeklySummary();

    /**
     * 月汇总
     * @return mixed
     */
    public function onAvailableMonthlySummary();

    /**
     * 支付成功
     * @return mixed
     */
    public function onSucceededCharge();

    /**
     * 退款成功
     * @return mixed
     */
    public function onSucceededRefund();

    /**
     * 企业付款成功
     * @return mixed
     */
    public function onSucceededTransfer();

    /**
     * 红包发送成功
     * @return mixed
     */
    public function onSentRedEnvelope();

    /**
     * 红包已领取
     * @return mixed
     */
    public function onReceivedRedEnvelope();
}
