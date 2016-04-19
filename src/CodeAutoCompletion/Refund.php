<?php
namespace idarex\pingppyii2\CodeAutoCompletion;

use Pingpp\PingppObject;

class Refund extends PingppObject
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
    public $transaction_no;
}
