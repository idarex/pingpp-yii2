<?php

use idarex\pingppyii2\Channel;
use idarex\pingppyii2\ChannelException;

class ChannelTest extends TestCase
{
    /**
     * @expectedException \idarex\pingppyii2\ChannelException
     */
    public function testNotExistChannelException()
    {
        Channel::exists('dddddddddddddddddnot-exist-channel');
    }

    public function testExistChannel()
    {
        Channel::exists(Channel::ALIPAY);
    }
}
