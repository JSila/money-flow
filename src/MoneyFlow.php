<?php

namespace MoneyFlow;

use DateTime;
use Exception;
use MoneyFlow\Expense\Expense;
use MoneyFlow\Revenue\Revenue;

class MoneyFlow
{
    const PREDICTION_OPTIMISTIC_RATE  = 1;
    const PREDICTION_REALISTIC_RATE   = 2;
    const PREDICTION_PESSIMISTIC_RATE = 3;

    /**
     * Prediction rates
     *
     * @var array
     */
    public static $predictionRates = [
        self::PREDICTION_OPTIMISTIC_RATE  => 0.85, 
        self::PREDICTION_REALISTIC_RATE   => 0.70, 
        self::PREDICTION_PESSIMISTIC_RATE => 0.60, 
    ];

    /**
     * List of revenues
     *
     * @var Revenue[]
     */
    protected $revenues = [];

    /**
     * List of expenses
     *
     * @var Expense[]
     */
    protected $expenses = [];

    /**
     * Adds revenue
     *
     * @param Revenue $revenue
     */
    public function addRevenue(Revenue $revenue)
    {
        $this->revenues[] = $revenue;
        return $this;
    }

    /**
     * Adds expense
     *
     * @param Expense $expense
     */
    public function addExpense(Expense $expense)
    {
        $this->expenses[] = $expense;
        return $this;
    }

    /**
     * Returns revenues
     *
     * @return Revenue[]
     */
    public function getRevenues()
    {
        return $this->revenues;
    }

    /**
     * Returns expenses
     *
     * @return Expense[]
     */
    public function getExpenses()
    {
        return $this->expenses;
    }

    /**
     * Returns VAT for specific month
     *
     * @param  DateTime $date
     * @return double
     */
    public function calculateMonthlyVAT(DateTime $date)
    {
        $revenuesVAT = array_reduce($this->revenues, function ($VATSum, Revenue $revenue) use ($date) {
            return $VATSum + $this->getVAT($revenue, $date);
        }, 0);
        $expensesVAT = array_reduce($this->expenses, function ($VATSum, Expense $expense) use ($date) {
            return $VATSum + $this->getVAT($expense, $date);
        }, 0);
        return $revenuesVAT - $expensesVAT;
    }

    /**
     * Get VAT from revenue/expense based on a date of interest and revenue/expense repeat status
     *
     * @param  Expense|Revenue $moneyType
     * @param  DateTime        $date
     * @return double
     */
    public function getVAT($moneyType, DateTime $date)
    {
        if ($moneyType->getVATCalculationMonth() != $date->format('n')) {
            return 0;
        }

        $moneyTypeDate = $moneyType->getDate() ?: $moneyType->getDate(true);
        if (!$moneyType->isRepeating() && $moneyTypeDate->format('Y') != $date->format('Y')) {
            return 0;
        }

        return $moneyType->getVAT();
    }

    /**
     * Returns balance for specific date in the past
     *
     * @param  DateTime $date
     * @throws Exception
     * @return double
     */
    public function getBalance(DateTime $date)
    {
        if ($date > new DateTime('now')) {
            throw new Exception('Can\'t tell you the balance for the future');
        }
        $revenues = array_reduce($this->revenues, function ($sum, Revenue $revenue) use ($date) {
            $revenueTotalValue = $revenue->getDate() <= $date ? $revenue->getTotalValue() : 0;

            return $sum + $revenueTotalValue * $this->monthsBetween($revenue->getDate(), $date);
        }, 0);
        $expenses = array_reduce($this->expenses, function ($sum, Expense $expense) use ($date) {
            $expenseTotalValue = $expense->getDate() <= $date ? $expense->getTotalValue() : 0;
            return $sum + $expenseTotalValue * $this->monthsBetween($expense->getDate(), $date);
        }, 0);
        return $revenues - $expenses;
    }

    /**
     * Internal function for getting number of months between two dates
     *
     * @param  DateTime $date
     * @return int
     */
    private function monthsBetween($date1, $date2)
    {
        $interval = date_diff($date1, $date2);
        return $interval->m + ($interval->y * 12) + 1;
    }

    /**
     * Calculated money balance for the future based on prediction type (optimistic, pessimistic, realistic).
     * It counts all predicted inflows and outflows from current date to specified future date.
     *
     * @param  DateTime $futureDate
     * @throws Exception
     * @return double
     */
    public function getBalancePrediction(DateTime $date, $predictionRate = 2)
    {
        if ($date <= new DateTime('now')) {
            throw new Exception('Can\'t predict balance for the past');
        }
        if (empty(self::$predictionRates[$predictionRate])) {
            throw new Exception('Invalid prediction rate');
        }
        $revenues = array_reduce($this->revenues, function ($revenueSum, Revenue $revenue) use ($date) {
            $predictedInflowsValue = array_reduce($revenue->getInflows(), function($inflowSum, $inflow) use ($date) {
                $inflowValue = (!is_null($inflow->getProbability()) && $inflow->getDate() < $date) ? $inflow->getValue() : 0;
                return $inflowSum + $inflowValue;
            }, 0);
            return $revenueSum + $predictedInflowsValue;
        }, 0);
        $expenses = array_reduce($this->expenses, function ($expenseSum, Expense $expense) use ($date) {
            $predictedOutflowsValue = array_reduce($expense->getOutflows(), function($outflowSum, $outflow) use ($date) {
                $outflowValue = (!is_null($outflow->getProbability()) && $outflow->getDate() < $date) ? $outflow->getValue() : 0;
                return $outflowSum + $outflowValue;
            }, 0);
            return $expenseSum + $predictedOutflowsValue;
        }, 0);

        return ($revenues - $expenses) * self::$predictionRates[$predictionRate];
    }
}
