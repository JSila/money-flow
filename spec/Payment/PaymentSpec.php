<?php

namespace spec\MoneyFlow\Payment;

use MoneyFlow\Payment\Payment;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PaymentSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(Payment::TYPE_BANKACCOUNT);
    }

    public function it_accepts_additional_data_about_payment()
    {
        $this->set('bank_name', 'NLB');
        $this->get('bank_name')->shouldReturn('NLB');
    }

    public function it_throws_error_if_additional_data_doesnt_exist()
    {
        $this->shouldThrow('\Exception')->duringGet('bank_name');
    }
}
