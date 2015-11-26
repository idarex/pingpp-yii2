<?php

$params = [
    // 这个订单必须要支付完成
    // 模拟支付完成只需要 GET 请求
    // https://api.pingxx.com/notify/charges/CHARGE_ID?livemode=false
    'refunds.chId' => 'ch_OKuHKCzrLWvLSSKinHj1uf1S',
    'retrieve.chId' => 'ch_OKuHKCzrLWvLSSKinHj1uf1S',
];
return [
    'id' => 'tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'pingpp' => [
            'class' => '\idarex\pingppyii2\PingppComponent',
            'appId' => 'app_1Gqj58ynP0mHeX1q',
            'apiKey' => 'sk_test_ibbTe5jLGCi5rzfH4OqPW9KC',
        ],
    ],
    'params' => $params,
];