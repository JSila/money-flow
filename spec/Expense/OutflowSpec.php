<?php

namespace spec\MoneyFlow\Expense;

use DateTime;
use MoneyFlow\Payment\Payment;
use MoneyFlow\Expense\Expense;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OutflowSpec extends ObjectBehavior
{
    public function let(DateTime $date, Expense $expense, Payment $payment)
    {
        $this->beConstructedWith(200, $date, $expense, $payment);
    }

    public function it_sets_expense_to_which_it_belong_and_adds_itself_to_that_expense_outflows(Expense $expense1)
    {
        $expense1->addOutflow($this)->shouldBeCalled();
        $this->setExpense($expense1);
        $this->getExpense()->shouldReturnAnInstanceOf('\MoneyFlow\Expense\Expense');
    }
}
