<?php

namespace MoneyFlow\Revenue;

use DateTime;
use Exception;
use MoneyFlow\Category\Category;
use MoneyFlow\Revenue\Inflow;

class Revenue
{
    /**
     * Revenue VAT rate (in %)
     *
     * @var double
     */
    public static $VATRate = 0.22;

    /**
     * Planned date of a revenue
     *
     * @var DateTime
     */
    protected $datePlanned;
    
    /**
     * Real date of a revenue
     *
     * @var DateTime
     */
    protected $dateReal;

    /**
     * Revenue value (without VAT)
     *
     * @var double
     */
    protected $value = 0;

    /**
     * Revenue total value (with VAT)
     *
     * @var double
     */
    protected $totalValue = 0;

    /**
     * Revenue value-added tax
     *
     * @var double
     */
    protected $VAT = 0;

    /**
     * Revenue title
     *
     * @var string
     */
    protected $title;

    /**
     * Revenue note
     *
     * @var string
     */
    protected $note;

    /**
     * Revenue category
     *
     * @var Category
     */
    protected $category;

    /**
     * Revenue inflows
     *
     * @var Inflow[]
     */
    protected $inflows = [];

    /**
     * Revenue repeat status
     *
     * @var boolean
     */
    protected $repeat = false;

    /**
     * Revenue constructor
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
     * Add inflow to revenue inflows
     *
     * @param  Inflow $inflow
     * @return Revenue
     */
    public function addInflow(Inflow $inflow)
    {
        $this->inflows[] = $inflow;
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
     * Set if revenue is repeated every month
     *
     * @param  boolean $repeat
     * @return Revenue
     */
    public function setRepeating($repeat = true)
    {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * Is revenue repeating every month?
     *
     * @return boolean $repeat
     */
    public function isRepeating()
    {
        return $this->repeat;
    }

    /**
     * Returns month when VAT for revenue is calculated.
     *
     * @throws Exception
     * @return int
     */
    public function getVATCalculationMonth()
    {
        if (empty($this->dateReal) && empty($this->datePlanned)) {
            throw new Exception('Please set revenue date first (planned or real)');
        }
        $dateType = $this->datePlanned ? 'datePlanned' : 'dateReal';
        return $this->{$dateType}->format('n');
    }

    /**
     * Returns revenue value (without VAT)
     *
     * @return double
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get total revenue value (with VAT)
     *
     * @return double
     */
    public function getTotalValue()
    {
        return $this->totalValue;
    }

    /**
     * Returns revenue category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Returns revenue title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns revenue note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Returns revenue planned date or date of creation
     *
     * @param  boolean $planned
     * @return DateTime
     */
    public function getDate($planned = false)
    {
        return $planned ? $this->datePlanned : $this->dateReal;
    }

    /**
     * Returns revenue inflows
     *
     * @return Inflow[]
     */
    public function getInflows()
    {
        return $this->inflows;
    }

    /**
     * Returns revenue value-added tax
     *
     * @return double
     */
    public function getVAT()
    {
        return $this->VAT;
    }
}
