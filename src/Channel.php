<?php

namespace idarex\pingppyii2;

/**
 * Class Channel
 * @package idarex\pingppyii2
 * @see https://pingxx.com/guidance/config
 */
class Channel
{
    //////// app \\\\\\\\\\
    const ALIPAY = 'alipay';
    const WX = 'wx';
    const UPACP = 'upacp';
    const UPMP = 'upmp';
    const BFB = 'bfb';
    const APPLE_PAY = 'apple_pay';

    //////// mobile browser \\\\\\\\\\
    const ALIPAY_WAP = 'alipay_wap';
    const WX_PUB = 'wx_pub';
    const UPACP_WAP = 'upacp_wap';
    const UPMP_WAP = 'upmp_wap';
    const BFB_WAP = 'bfb_wap';
    const YEEPAY_WAP = 'yeepay_wap';
    const JDPAY_WAP = 'jdpay_wap';

    //////// pc browser \\\\\\\\\\
    const ALIPAY_PC_DIRECT = 'alipay_pc_direct';
    const UPACP_PC = 'upacp_pc';

    //////// special \\\\\\\\\\
    const ALIPAY_QR = 'alipay_qr';
    const WX_PUB_QR = 'wx_pub_qr';

    public static $allChannel = [
        self::ALIPAY,
        self::WX,
        self::UPACP,
        self::UPMP,
        self::BFB,
        self::APPLE_PAY,
        self::ALIPAY_WAP,
        self::WX_PUB,
        self::UPACP_WAP,
        self::UPMP_WAP,
        self::BFB_WAP,
        self::YEEPAY_WAP,
        self::JDPAY_WAP,
        self::ALIPAY_PC_DIRECT,
        self::WX_PUB_QR,
    ];

    /**
     * Check channel exists
     *
     * @param string $channel
     * @throws ChannelException channel not exists
     */
    public static function exists($channel)
    {
        if (!in_array($channel, self::$allChannel, true)) {
            throw new ChannelException("channel '{$channel}' is not exists.");
        }
    }
}
