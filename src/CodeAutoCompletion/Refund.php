<?php
namespace idarex\pingppyii2\CodeAutoCompletion;

use Pingpp\Object;

class Refund extends Object
{
    public $id;
    public $object;
    public $order_no;
    public $amount;
    public $created;
    public $succeed;
    public $status;
    public $time_succeed;
    public $description;
    public $failure_code;
    public $failure_msg;
    public $metadata;
    public $charge;
}
