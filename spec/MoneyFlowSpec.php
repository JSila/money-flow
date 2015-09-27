<?php

namespace spec\MoneyFlow;

use DateTime;
use MoneyFlow\Expense\Expense;
use MoneyFlow\Expense\Outflow;
use MoneyFlow\MoneyFlow;
use MoneyFlow\Revenue\Revenue;
use MoneyFlow\Revenue\Inflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoneyFlowSpec extends ObjectBehavior
{
    public function it_stores_revenues(Revenue $revenue)
    {
        $this->addRevenue($revenue);
        $this->getRevenues()->shouldHaveCount(1);

        $this->addRevenue($revenue)
            ->addRevenue($revenue);
        $this->getRevenues()->shouldHaveCount(3);
    }

    public function it_stores_expenses(Expense $expense)
    {
        $this->addExpense($expense);
        $this->getExpenses()->shouldHaveCount(1);

        $this->addExpense($expense)
            ->addExpense($expense);
        $this->getExpenses()->shouldHaveCount(3);
    }
    
    public function it_returns_VAT_0_if_months_arent_the_same(Revenue $revenue, DateTime $date)
    {
        $date->format('n')->shouldBeCalled()->willReturn(7);
        $revenue->getVATCalculationMonth()->shouldBeCalled()->willReturn(8);
        $this->getVAT($revenue, $date)->shouldReturn(0);
    }

    public function it_returns_VAT_0_if_revenue_set_as_not_repeatable_and_months_are_the_same_but_not_years(Revenue $revenue, DateTime $date)
    {
        $date->format('n')->shouldBeCalled()->willReturn(7);
        $revenue->getVATCalculationMonth()->shouldBeCalled()->willReturn(7);
        $revenue->getDate()->willReturn(new DateTime('2015-07-23'));
        $revenue->isRepeating()->willReturn(false);
        $date->format('Y')->shouldBeCalled()->willReturn('2014');
        $this->getVAT($revenue, $date)->shouldReturn(0);
    }

    public function it_returns_VAT_if_revenue_set_as_not_repeatable_and_months_and_years_are_the_same(Revenue $revenue, DateTime $date)
    {
        $date->format('n')->shouldBeCalled()->willReturn(7);
        $revenue->getVATCalculationMonth()->shouldBeCalled()->willReturn(7);
        $revenue->getDate()->willReturn(new DateTime('2014-07-23'));
        $revenue->isRepeating()->willReturn(false);
        $date->format('Y')->shouldBeCalled()->willReturn('2014');
        $revenue->getVAT()->shouldBeCalled()->willReturn(231);
        $this->getVAT($revenue, $date)->shouldReturn(231);
    }

    public function it_returns_VAT_if_revenue_set_as_repeatable_and_months_are_the_same(Revenue $revenue, DateTime $date)
    {
        $date->format('n')->shouldBeCalled()->willReturn(7);
        $revenue->getVATCalculationMonth()->shouldBeCalled()->willReturn(7);
        $revenue->getDate()->willReturn(new DateTime('2014-07-23'));
        $revenue->isRepeating()->willReturn(true);
        $revenue->getVAT()->shouldBeCalled()->willReturn(231);
        $this->getVAT($revenue, $date)->shouldReturn(231);
    }

    public function it_calculates_balance_for_day_in_the_past_after_some_expenses_and_revenues_where_made(Revenue $revenue, Expense $expense)
    {
        $date = new DateTime('-7 days');
        $revenue->getDate()->shouldBeCalled()->willReturn(new DateTime('-11 days'));
        $revenue->getTotalValue()->shouldBeCalled()->willReturn(100);
        $expense->getDate()->shouldBeCalled()->willReturn(new DateTime('-11 days'));
        $expense->getTotalValue()->shouldBeCalled()->willReturn(50);

        $this->addRevenue($revenue)
            ->addExpense($expense);

        $this->getBalance($date)->shouldReturn(50);
    }

    public function it_returns_balance_0_for_day_in_the_past_before_some_expenses_and_revenues_where_made(Revenue $revenue, Expense $expense)
    {
        $date = new DateTime('-15 days');
        $revenue->getDate()->shouldBeCalled()->willReturn(new DateTime('-11 days'));
        $expense->getDate()->shouldBeCalled()->willReturn(new DateTime('-11 days'));

        $this->addRevenue($revenue)
            ->addExpense($expense);

        $this->getBalance($date)->shouldReturn(0);
    }

    public function it_throws_an_error_if_trying_to_get_a_balance_for_future_day()
    {
        $date = new DateTime('+7 days');
        $this->shouldThrow('\Exception')->duringGetBalance($date);
    }

    public function it_throws_an_error_if_trying_to_predict_a_balance_for_past_day()
    {
        $date = new DateTime('-7 days');
        $this->shouldThrow('\Exception')->duringGetBalancePrediction($date);
    }

    public function it_returns_predicted_balance_for_future_day(Revenue $revenue, Expense $expense, Inflow $inflow, Outflow $outflow, Expense $expense1)
    {
        $date = new DateTime('+15 days');
        
        $inflow->getProbability()->shouldBeCalled()->willReturn(0.73);
        $inflow->getDate()->shouldBeCalled()->willReturn(new DateTime('+2 days'));
        $inflow->getValue()->shouldBeCalled()->willReturn(120.0);
        $revenue->getInflows()->shouldBeCalled()->willReturn([$inflow]);

        $outflow->getProbability()->shouldBeCalled()->willReturn(0.83);
        $outflow->getDate()->shouldBeCalled()->willReturn(new DateTime('+3 days'));
        $outflow->getValue()->shouldBeCalled()->willReturn(50.0);
        $expense->getOutflows()->shouldBeCalled()->willReturn([$outflow]);

        $revenue->addInflow($inflow);
        $expense->addOutflow($outflow);

        $this->addRevenue($revenue)
            ->addExpense($expense);

        $this->getBalancePrediction($date)->shouldReturn(49.0);
    }
}
