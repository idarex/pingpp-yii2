<?php

use idarex\pingppyii2\Hooks;
use idarex\pingppyii2\PingppComponent;
use idarex\pingppyii2\HooksInterface;

class HooksTests extends TestCase
{
    public function testVerifySign()
    {
        $rawData = 'wrong raw data';
        $signature = 'wrong signature';

        $publicKey = file_get_contents(__DIR__ . "/rsa_public_key.pem");
        $this->assertFalse(Hooks::verifySign($rawData, $signature, $publicKey));

        $rawData = '{"id":"evt_eYa58Wd44Glerl8AgfYfd1sL","created":1434368075,"livemode":true,"type":"charge.succeeded","data":{"object":{"id":"ch_bq9IHKnn6GnLzsS0swOujr4x","object":"charge","created":1434368069,"livemode":true,"paid":true,"refunded":false,"app":"app_vcPcqDeS88ixrPlu","channel":"wx","order_no":"2015d019f7cf6c0d","client_ip":"140.227.22.72","amount":100,"amount_settle":0,"currency":"cny","subject":"An Apple","body":"A Big Red Apple","extra":{},"time_paid":1434368074,"time_expire":1434455469,"time_settle":null,"transaction_no":"1014400031201506150354653857","refunds":{"object":"list","url":"/v1/charges/ch_bq9IHKnn6GnLzsS0swOujr4x/refunds","has_more":false,"data":[]},"amount_refunded":0,"failure_code":null,"failure_msg":null,"metadata":{},"credential":{},"description":null}},"object":"event","pending_webhooks":0,"request":"iar_Xc2SGjrbdmT0eeKWeCsvLhbL"}';
        $signature = 'wrong signature';
        $this->assertFalse(Hooks::verifySign($rawData, $signature, $publicKey));

        $rawData = '{"id":"evt_eYa58Wd44Glerl8AgfYfd1sL","created":1434368075,"livemode":true,"type":"charge.succeeded","data":{"object":{"id":"ch_bq9IHKnn6GnLzsS0swOujr4x","object":"charge","created":1434368069,"livemode":true,"paid":true,"refunded":false,"app":"app_vcPcqDeS88ixrPlu","channel":"wx","order_no":"2015d019f7cf6c0d","client_ip":"140.227.22.72","amount":100,"amount_settle":0,"currency":"cny","subject":"An Apple","body":"A Big Red Apple","extra":{},"time_paid":1434368074,"time_expire":1434455469,"time_settle":null,"transaction_no":"1014400031201506150354653857","refunds":{"object":"list","url":"/v1/charges/ch_bq9IHKnn6GnLzsS0swOujr4x/refunds","has_more":false,"data":[]},"amount_refunded":0,"failure_code":null,"failure_msg":null,"metadata":{},"credential":{},"description":null}},"object":"event","pending_webhooks":0,"request":"iar_Xc2SGjrbdmT0eeKWeCsvLhbL"}';
        $signature = 'BX5sToHUzPSJvAfXqhtJicsuPjt3yvq804PguzLnMruCSvZ4C7xYS4trdg1blJPh26eeK/P2QfCCHpWKedsRS3bPKkjAvugnMKs+3Zs1k+PshAiZsET4sWPGNnf1E89Kh7/2XMa1mgbXtHt7zPNC4kamTqUL/QmEVI8LJNq7C9P3LR03kK2szJDhPzkWPgRyY2YpD2eq1aCJm0bkX9mBWTZdSYFhKt3vuM1Qjp5PWXk0tN5h9dNFqpisihK7XboB81poER2SmnZ8PIslzWu2iULM7VWxmEDA70JKBJFweqLCFBHRszA8Nt3AXF0z5qe61oH1oSUmtPwNhdQQ2G5X3g==';
        $this->assertTrue(Hooks::verifySign($rawData, $signature, $publicKey));
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testInvalidComponentException()
    {
        $hooks = $this->getHooks();
        $hooks->pingpp = 'wrong-component-name';
        $hooks->getComponent();
    }

    public function testGetComponentByComponentId()
    {
        $hooks = $this->getHooks();
        $hooks->pingpp = 'pingpp';
        $this->assertTrue($hooks->getComponent() instanceof PingppComponent);
    }

    public function testGetComponentByInstance()
    {
        $hooks = $this->getHooks();
        $hooks->pingpp = Yii::$app->get('pingpp');
        $this->assertTrue($hooks->getComponent() instanceof PingppComponent);
    }

    public function testPublicKeyProperty()
    {
        $hooks = $this->getHooks();
        $hooks->publicKey = '123';
        $this->assertEquals('123', $hooks->publicKey);
    }

    public function testSetPublicKeyPath()
    {
        $hooks = $this->getHooks();
        $hooks->publicKeyPath = __DIR__ . "/rsa_public_key.pem";
    }

    /**
     * @expectedException \yii\web\ForbiddenHttpException
     */
    public function testInvalidPublicKeyPathException()
    {
        $hooks = $this->getHooks();
        $hooks->headers = 'wrong_header';
        $hooks->publicKeyPath = __DIR__ . "/rsa_public_key.pem";
        $hooks->rawData = 'wrong_data';
        $hooks->signature = 'wrong_signature';
        $hooks->run();
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testForbiddenException()
    {
        $hooks = $this->getHooks();
        $hooks->publicKeyPath = __DIR__ . "/not-exist.file";
    }

    public function testRoute()
    {
        /* @var Hooks $hooks */
        $hooks = Yii::createObject([
            'class' => PingppHooksComponent::className(),
        ]);
        $hooks->route(HooksInterface::SUMMARY_DAILY_AVAILABLE);
        $hooks->route(HooksInterface::SUMMARY_MONTHLY_AVAILABLE);
        $hooks->route(HooksInterface::SUMMARY_WEEKLY_AVAILABLE);
        $hooks->route(HooksInterface::CHARGE_SUCCEEDED);
        $hooks->route(HooksInterface::REFUND_SUCCEEDED);
        $hooks->route(HooksInterface::RED_ENVELOPE_RECEIVED);
        $hooks->route(HooksInterface::RED_ENVELOPE_SENT);
        $hooks->route(HooksInterface::TRANSFER_SUCCEEDED);
    }

    /**
     * @expectedException \yii\web\BadRequestHttpException
     */
    public function testRouteException()
    {
        /* @var Hooks $hooks */
        $hooks = Yii::createObject([
            'class' => PingppHooksComponent::className(),
        ]);
        $hooks->route('not-exist-route');
    }

    /**
     * @return Hooks
     * @throws \yii\base\InvalidConfigException
     */
    protected function getHooks()
    {
        return Yii::createObject([
            'class' => Hooks::className(),
        ]);
    }
}
