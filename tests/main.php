<?php

return [
    'id' => 'tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'pingpp' => [
            'class' => '\idarex\pingppyii2\PingppComponent',
            'appId' => 'app_1Gqj58ynP0mHeX1q',
            'apiKey' => 'sk_test_ibbTe5jLGCi5rzfH4OqPW9KC',
            'wxAppId' => 'wx60ab301bf65da4b1',
            'wxAppSecret' => '697749a4470b5e7b3d21e677cda125f6',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'params' => [],
];
