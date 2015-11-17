<?php

namespace idarex\pingppyii2;

class ChannelException extends \Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'ChannelException';
    }
}
