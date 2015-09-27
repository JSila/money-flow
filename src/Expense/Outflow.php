<?php

namespace MoneyFlow\Expense;

use DateTime;
use MoneyFlow\Payment\Payment;
use MoneyFlow\Expense\Expense;

class Outflow
{
    /**
     * Expense to which outflow belongs
     *
     * @var Expense
     */
    protected $expense;

    /**
     * Outflow date
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Outflow payment
     *
     * @var Payment
     */
    protected $payment;

    /**
     * Outflow probability (in %)
     *
     * @var int
     */
    protected $probability;

    /**
     * Outflow value (in cents)
     *
     * @var int
     */
    protected $value;

    /**
     * Outflow constructor
     *
     * @param int      $value
     * @param DateTime $date
     * @param Expense  $expense
     * @param Payment  $payment
     */
    public function __construct($value, DateTime $date, Expense $expense, Payment $payment)
    {
        $this->value   = $value;
        $this->date    = $date;
        $this->payment = $payment;

        $this->setExpense($expense);
    }

    /**
     * Returns outflow expense
     *
     * @return Revenue
     */
    public function getExpense()
    {
        return $this->expense;
    }

    /**
     * Sets outflow expense
     *
     * @return Outflow
     */
    public function setExpense($expense)
    {
        $this->expense = $expense;
        $this->expense->addOutflow($this);
        return $this;
    }

    /**
     * Returns outflow date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Returns outflow payment
     *
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Returns the probability of outflow (in %)
     *
     * @return int
     */
    public function getProbability()
    {
        return $this->probability;
    }

    /**
     * Returns outflow value (in cents)
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the outflow probability (in %).
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
