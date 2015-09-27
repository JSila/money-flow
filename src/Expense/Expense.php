<?php

namespace MoneyFlow\Expense;

use DateTime;
use Exception;
use MoneyFlow\Category\Category;
use MoneyFlow\Expense\Outflow;

class Expense
{
    /**
     * Expense VAT rate (in %)
     *
     * @var double
     */
    public static $VATRate = 0.22;

    /**
     * Planned date of a expense
     *
     * @var DateTime
     */
    protected $datePlanned;
    
    /**
     * Real date of a expense
     *
     * @var DateTime
     */
    protected $dateReal;

    /**
     * Expense value (without VAT)
     *
     * @var double
     */
    protected $value = 0;

    /**
     * Expense total value (with VAT)
     *
     * @var double
     */
    protected $totalValue = 0;

    /**
     * Expense value-added tax
     *
     * @var double
     */
    protected $VAT = 0;

    /**
     * Expense title
     *
     * @var string
     */
    protected $title;

    /**
     * Expense note
     *
     * @var string
     */
    protected $note;

    /**
     * Expense category
     *
     * @var Category
     */
    protected $category;

    /**
     * Expense inflows
     *
     * @var Outflow[]
     */
    protected $outflows = [];

    /**
     * Expense repeat status
     *
     * @var boolean
     */
    protected $repeat = false;

    /**
     * Expense constructor
     *
     * @param double    $value
     * @param string    $title
     * @param string    $note
     * @param Category  $category
     */
    public function __construct($value, $title, $note, Category $category)
    {
        $this->value      = $value;
        $this->VAT        = $value * self::$VATRate;
        $this->totalValue = $value + $this->VAT;
        $this->title      = $title;
        $this->note       = $note;
        $this->category   = $category;
    }

    /**
     * Add outflow to expense outflows
     *
     * @param  Outflow $outflow
     * @return Expense
     */
    public function addOutflow(Outflow $outflow)
    {
        $this->outflows[] = $outflow;
        return $this;
    }

    /**
     * Set planned date or date of creation
     *
     * @param DateTime  $date
     * @param boolean   $planned
     */
    public function setDate(DateTime $date, $planned = false)
    {
        if ($planned) {
            $this->datePlanned = $date;
            $this->dateReal    = null;
        } else {
            $this->dateReal   = $date;
            $this->datePlanned = null;
        }
        return $this;
    }

    /**
     * Set if expense is repeated every month
     *
     * @param  boolean $repeat
     * @return Expense
     */
    public function setRepeating($repeat = true)
    {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * Is expense repeating every month?
     *
     * @return boolean $repeat
     */
    public function isRepeating()
    {
        return $this->repeat;
    }

    /**
     * Returns month when VAT for expense is calculated
     *
     * @throws Exception
     * @return int
     */
    public function getVATCalculationMonth()
    {
        if (empty($this->dateReal) && empty($this->datePlanned)) {
            throw new Exception('Please set expense date first (planned or real)');
        }
        $dateType = $this->datePlanned ? 'datePlanned' : 'dateReal';
        return $this->{$dateType}->format('n');
    }

    /**
     * Returns expense value (without VAT)
     *
     * @return double
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get total expense value (with VAT)
     *
     * @return double
     */
    public function getTotalValue()
    {
        return $this->totalValue;
    }

    /**
     * Returns expense category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Returns expense title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns expense note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Returns expense planned date or date of creation
     *
     * @param  boolean $planned
     * @return DateTime
     */
    public function getDate($planned = false)
    {
        return $planned ? $this->datePlanned : $this->dateReal;
    }

    /**
     * Returns expense outflows
     *
     * @return Expense[]
     */
    public function getOutflows()
    {
        return $this->outflows;
    }

    /**
     * Returns expense value-added tax
     *
     * @return double
     */
    public function getVAT()
    {
        return $this->VAT;
    }
}
