<?php

namespace idarex\pingppyii2\CodeAutoCompletion;

use Pingpp\PingppObject;

class ListObj extends PingppObject
{
    /**
     * @var string name of the object
     */
    public $object;
    /**
     * @var string
     */
    public $url;
    /**
     * @var boolean
     */
    public $has_more;
    /**
     * @var array
     * @see Charge|Refund
     */
    public $data;
}
