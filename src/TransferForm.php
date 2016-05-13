<?php

namespace idarex\pingppyii2;

use Pingpp\Transfer;

class TransferForm extends Model
{
    public $amount;
    public $order_no;
    public $currency;
    public $channel;
    public $type;
    public $recipient;
    public $description;
    public $user_name;
    public $force_check;

    public function rules()
    {
        return array_merge([
            [
                [
                    'order_no',
                    'amount',
                    'channel',
                    'currency',
                    'type',
                    'description',
                    'recipient',
                    'user_name',
                    'force_check'
                ],
                'required'
            ],
            [['order_no', 'amount'], 'number', 'min' => 1],
            ['order_no', 'string', 'max' => 50],
            ['amount', 'number', 'min' => 1],
            ['channel', 'in', 'range' => [Channel::WX, Channel::WX_PUB]],
            ['type', 'in', 'range' => ['b2c']],
            [
                'currency',
                function ($attribute) {
                    if ($this->$attribute != 'cny') {
                        $this->addError($attribute, "The currency must be 'cny'");
                    }
                }
            ],
            [['description'], 'string', 'max' => 255],
            [['user_name', 'force_check'], 'safe'],
        ], parent::rules());
    }


    public function create($params = null, $options = null)
    {
        if ($this->validate()) {

            $extra = array_merge([
                'user_name' => $this->user_name,
                'force_check' => $this->force_check,
            ], $this->extra);

            $this->transfer = Transfer::create([
                'amount' => $this->amount,
                'order_no' => $this->order_no,
                'currency' => $this->currency,
                'channel' => $this->channel,
                'app' => [
                    'id' => $this->getComponent()->appId,
                ],
                'type' => $this->type,
                'recipient' => $this->recipient,
                'description' => $this->description,
                'extra' => $extra,
                'metadata' => $this->metadata,
            ], $params, $options);

            return (bool)$this->transfer;
        }

        return false;
    }

    /**
     * @var Transfer
     */
    protected $transfer;

    public function getData($asArray = false)
    {
        if ($asArray) {
            return $this->transfer->__toArray(true);
        }

        return $this->transfer->__toStdObject();
    }
}
