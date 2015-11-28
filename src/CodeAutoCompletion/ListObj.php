<?php

namespace idarex\pingppyii2\CodeAutoCompletion;

use Pingpp\Object;

class ListObj extends Object
{
    /**
     * @var string
     */
    public $url;
    /**
     * @var boolean
     */
    public $has_more;
    /**
     * @var array Charge List
     * @see Charge
     */
    public $data;
}
