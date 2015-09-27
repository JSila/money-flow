<?php

namespace MoneyFlow\Payment;

use Exception;

class Payment
{
    const TYPE_BANKACCOUNT = 1;

    /**
     * Available payment types
     *
     * @var array
     */
    public static $types = [
        self::TYPE_BANKACCOUNT => 'Bank Account'
    ];

    /**
     * Payment type
     *
     * @var int
     */
    protected $type;

    /**
     * Payment type data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Payment constructor
     *
     * @param int $type
     * @throws Exception
     */
    public function __construct($type)
    {
        if (empty(self::$types[$type])) {
            throw new Exception('Invalid payment type');
        }
        $this->type = $type;
    }

    /**
     * Returns payment type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns payment name
     *
     * @return string
     */
    public function getName()
    {
        return self::$types[$this->type];
    }

    /**
     * Sets payment type detail
     *
     * @param string  $key
     * @param string  $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Returns payment type detail
     *
     * @param  string $key
     * @throws Exception
     * @return mixed
     */
    public function get($key)
    {
        if (empty($this->data[$key])) {
            throw new Exception(sprintf('Key %s doesnt\'t exists in payments type details.', $key));
        }
        return $this->data[$key];
    }
}
