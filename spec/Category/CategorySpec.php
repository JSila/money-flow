<?php

namespace spec\MoneyFlow\Category;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CategorySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('title', 'description');
    }
}
