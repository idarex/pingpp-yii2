Pingpp Extension for Yii2
=================

基于 Ping++ 官方的 SDK 进行了简单的封装，用于 Yii2 框架。

[![Build Status](https://travis-ci.org/idarex/pingpp-yii2.svg)](https://travis-ci.org/idarex/pingpp-yii2)
[![Code Climate](https://codeclimate.com/github/idarex/pingpp-yii2/badges/gpa.svg)](https://codeclimate.com/github/idarex/pingpp-yii2)
[![Issue Count](https://codeclimate.com/github/idarex/pingpp-yii2/badges/issue_count.svg)](https://codeclimate.com/github/idarex/pingpp-yii2)
[![Latest Stable Version](https://poser.pugx.org/idarex/pingpp-yii2/version)](https://packagist.org/packages/idarex/pingpp-yii2)
[![Latest Unstable Version](https://poser.pugx.org/idarex/pingpp-yii2/v/unstable)](//packagist.org/packages/idarex/pingpp-yii2)
[![Total Downloads](https://poser.pugx.org/idarex/pingpp-yii2/downloads)](https://packagist.org/packages/idarex/pingpp-yii2)
[![License](https://poser.pugx.org/idarex/pingpp-yii2/license)](https://packagist.org/packages/idarex/pingpp-yii2)

Installation
--------------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist idarex/pingpp-yii2
```

or add

```json
"idarex/pingpp-yii2": "dev-master"
```

to the `require` section of your composer.json.


Configuration
--------------------

To use this extension, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'pingpp' => [
            'class' => '\idarex\pingppyii2\PingppComponent',
            'apiKey' => '<YOUR_API_KEY>',
            'appId' => '<YOUR_APP_ID>',
        ],
    ],
];
```

使用
--------------------

#### 支付

##### 付款

```php
use Yii;
use yii\web\ServerErrorHttpException;
use idarex\pingppyii2\Channel;
use idarex\pingppyii2\ChargeForm;

$chargeForm = new ChargeForm();
$chargeForm->order_no = '123456789';
$chargeForm->amount = '100';
/**
 * @see Channel
 */
$chargeForm->channel = Channel::WX;
$chargeForm->currency = 'cny';
$chargeForm->client_ip = Yii::$app->getRequest()->userIP;
$chargeForm->subject = 'Your Subject';
$chargeForm->body = 'Your body';

if ($response = $chargeForm->create()) {
    return $response->__toArray(true);
} elseif ($chargeForm->hasErrors()) {
    var_dump($chargeForm->getErrors());
} else {
    throw new ServerErrorHttpException();
}
```

##### 退款

```php
\Yii::$app->pingpp->refunds($chId, $amount, $description);
```

##### 查询

查询单笔交易

```php
\Yii::$app->pingpp->retrieve($chId);

```

查询交易列表

```php
$params = ['limit' => 1,];
\Yii::$app->pingpp->chargeList($params);
```

查询单笔退款

```php
\Yii::$app->pingpp->refundRetrieve($chId, $refundId);
```

查询退款列表

```php
$params = ['limit' => 1];
\Yii::$app->pingpp->refundRetrieveList($chId, $params);
```

#### 红包

##### 发送红包

coming soon

##### 查询红包

coming soon

#### 微信企业付款

##### 付款

coming soon

##### 查询

coming soon

#### 接收 Webhooks 通知

##### Configuration

Modify your controler, add or change methode `actions()`

```php

/**
 * @inheritdoc
 */
public function beforeAction($action)
{
    if ($action->id == 'pingpp-hooks') {
        // 当用户完成交易后 Ping++ 会以 POST 方式把数据发送到你的 hook 地址
        // 所以这时候需要临时关闭掉 Yii 的 CSRF 验证
        Yii::$app->controller->enableCsrfValidation = false;
    }

    return parent::beforeAction($action);
}

public function actions()
{
	return [
        // ...
        'pingpp-hooks' => [
            'class' => '\idarex\pingppyii2\HooksAction',
            'pingppHooksComponentClass' => 'common\components\PingppHooks',
            'publicKeyPath' => '@common/config/pingpp_rsa_public_key.pem',
        ],
    ];
}
```

##### 写自己的 Webhook 业务

\#file: common/components/PingppHooks.php

* 使用 `$this->event` 来访问 Ping++ 提交过来的数据
* 用 `Yii::$app->getResponse()->data = '';` 来给返回值赋值。
* 方法最后调用 `Yii::$app->end();` 来结束请求。

```php
<?php

namespace common\components;

use idarex\pingppyii2\Hooks;
use idarex\pingppyii2\HooksInterface;
use Yii;

class PingppHooks extends Hooks implements HooksInterface
{
    /**
     * @inheritdoc
     */
    public function onAvailableDailySummary()
    {
        Yii::$app->end();
    }

    /**
     * @inheritdoc
     */
    public function onAvailableWeeklySummary()
    {
        Yii::$app->end();
    }

    /**
     * @inheritdoc
     */
    public function onAvailableMonthlySummary()
    {
        Yii::$app->end();
    }

    /**
     * @inheritdoc
     */
    public function onSucceededCharge()
    {
        $orderId = $this->event->data->object->order_no;
        Yii::$app->getResponse()->data = 'finished job';
        Yii::$app->end();
    }

    /**
     * @inheritdoc
     */
    public function onSucceededRefund()
    {
        Yii::$app->end();
    }

    /**
     * @inheritdoc
     */
    public function onSucceededTransfer()
    {
        Yii::$app->end();
    }

    /**
     * @inheritdoc
     */
    public function onSentRedEnvelope()
    {
        Yii::$app->end();
    }

    /**
     * @inheritdoc
     */
    public function onReceivedRedEnvelope()
    {
        Yii::$app->end();
    }
}
```

Tricks
--------------------

* 给配置的组件加 IDE 自动补全 [IDE autocompletion for custom components](https://github.com/samdark/yii2-cookbook/blob/master/book/ide-autocompletion.md)
* 手动标记一个测试环境的订单为已支付，使用 `GET` 请求 `https://api.pingxx.com/notify/charges/CHARGE_ID?livemode=false`
* 调用组件的相应方法后，会有对该接口返回值的对象属性自动补全功能
