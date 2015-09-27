<?php

namespace spec\MoneyFlow\Revenue;

use DateTime;
use MoneyFlow\Payment\Payment;
use MoneyFlow\Revenue\Revenue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InflowSpec extends ObjectBehavior
{
    public function let(DateTime $date, Revenue $revenue, Payment $payment)
    {
        $this->beConstructedWith(200, $date, $revenue, $payment);
    }

    public function it_sets_revenue_to_which_it_belong_and_adds_itself_to_that_revenue_inflows(Revenue $revenue1)
    {
        $revenue1->addInflow($this)->shouldBeCalled();
        $this->setRevenue($revenue1);
        $this->getRevenue()->shouldReturnAnInstanceOf('\MoneyFlow\Revenue\Revenue');
    }
}
