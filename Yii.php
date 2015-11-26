<?php

/**
 * Yii bootstrap file.
 * Used for enhanced IDE code auto completion.
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * Class ConsoleApplication
 * Include only console application related components here
 *
 * @property \idarex\pingppyii2\PingppComponent $pingpp Ping Plus Plus
 */
class ConsoleApplication extends yii\console\Application
{
}
