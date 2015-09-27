<?php

namespace MoneyFlow\Revenue;

use DateTime;
use MoneyFlow\Payment\Payment;
use MoneyFlow\Revenue\Revenue;

class Inflow
{
    /**
     * Revenue to which inflow belongs
     *
     * @var Revenue
     */
    protected $revenue;

    /**
     * Inflow date
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Inflow payment
     *
     * @var Payment
     */
    protected $payment;

    /**
     * Inflow probability (in %)
     *
     * @var int
     */
    protected $probability;

    /**
     * Inflow value (in cents)
     *
     * @var int
     */
    protected $value;

    /**
     * Inflow constructor
     *
     * @param int        $value
     * @param DateTime   $date
     * @param Revenue    $revenue
     * @param Payment    $payment
     */
    public function __construct($value, DateTime $date, Revenue $revenue, Payment $payment)
    {
        $this->value   = $value;
        $this->date    = $date;
        $this->payment = $payment;

        $this->setRevenue($revenue);
    }

    /**
     * Returns inflow revenue
     *
     * @return Revenue
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * Sets inflow revenue
     *
     * @return Inflow
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;
        $this->revenue->addInflow($this);
        return $this;
    }

    /**
     * Returns inflow date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Returns inflow payment
     *
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Returns the probability of inflow (in %)
     *
     * @return int
     */
    public function getProbability()
    {
        return $this->probability;
    }

    /**
     * Returns inflow value (in cents)
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the Inflow probability (in %).
     *
     * @param int|null $probability if null, inflow is not expectation
     * @return Inflow
     */
    public function setProbability($probability = null)
    {
        $this->probability = $probability;
        return $this;
    }
}
