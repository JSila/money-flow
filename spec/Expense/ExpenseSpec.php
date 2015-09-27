<?php

namespace spec\MoneyFlow\Expense;

use DateTime;
use MoneyFlow\Category\Category;
use MoneyFlow\Expense\Outflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExpenseSpec extends ObjectBehavior
{
    public function let(Category $category)
    {
        $this->beConstructedWith(100, 'title', 'note', $category);
    }

    public function it_calculates_value_and_total_value_with_VAT_upon_creation()
    {
        $this->getTotalValue()->shouldReturn(122.0);
        $this->getVAT()->shouldReturn(22.0);
    }

    public function it_stores_inflows(Outflow $outflow)
    {
        $this->addOutflow($outflow);
        $this->getOutflows()->shouldHaveCount(1);

        $this->addOutflow($outflow)
            ->addOutflow($outflow);
        $this->getOutflows()->shouldHaveCount(3);
    }

    public function it_stores_real_date(DateTime $date)
    {
        $this->setDate($date);
        $this->getDate()->shouldReturnAnInstanceOf('\DateTime');
        $this->getDate(true)->shouldReturn(null);
    }

    public function it_stores_planned_date(DateTime $date)
    {
        $this->setDate($date, true);
        $this->getDate(true)->shouldReturnAnInstanceOf('\DateTime');
        $this->getDate()->shouldReturn(null);
    }

    public function it_returns_month_numbers_for_VAT_calculation_month_if_date_was_set(DateTime $date)
    {
        $date->format('n')->shouldBeCalled()->willReturn(4);

        $this->setDate($date)
            ->getVATCalculationMonth()
            ->shouldReturn(4);

        $date->format('n')->shouldBeCalled()->willReturn(5);

        $this->setDate($date, true)
            ->getVATCalculationMonth()
            ->shouldReturn(5);
    }

    public function it_throws_error_instead_of_month_numbers_for_VAT_calculation_month_if_date_not_present()
    {
        $this->shouldThrow('\Exception')->duringGetVATCalculationMonth();
    }
}
