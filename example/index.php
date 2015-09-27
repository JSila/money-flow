<?php

include '../vendor/autoload.php';

use MoneyFlow\Category\Category;
use MoneyFlow\Expense\Expense;
use MoneyFlow\Expense\Outflow;
use MoneyFlow\MoneyFlow;
use MoneyFlow\Payment\Payment;
use MoneyFlow\Revenue\Inflow;
use MoneyFlow\Revenue\Revenue;

$category = new Category('title', 'description');
$payment1 = new Payment(Payment::TYPE_BANKACCOUNT);
$payment2 = new Payment(Payment::TYPE_BANKACCOUNT);

$revenue = new Revenue(1000, 'Website development', 'Website development description', $category);
$revenue->setDate(new DateTime('-4 days'));
$inflow1 = new Inflow(600, new DateTime('-3 days'), $revenue, $payment1);
$inflow2 = new Inflow(400, new DateTime('-2 days'), $revenue, $payment2);

$expense = new Expense(500, 'WP theme purchase', 'WP theme purchase description', $category);
$expense->setDate(new DateTime('-4 days'));
$outflow1 = new Outflow(300, new DateTime('-3 days'), $expense, $payment1);
$outflow2 = new Outflow(200, new DateTime('-2 days'), $expense, $payment2);

$money = new MoneyFlow;
$money->addRevenue($revenue)->addExpense($expense);

// -- scenario 1: get balance and monthly vat

// echo $money->getBalance(new DateTime('-1 day'));  // 610
// echo $money->calculateMonthlyVAT(new DateTime('now'));  // 110

// -- scenario 2: same as scenario 1, but expense was created 4 months ago

// $expense->setDate(new DateTime('-4 months'));
// echo $money->getBalance(new DateTime('-1 day'));  // 610
// echo $money->calculateMonthlyVAT(new DateTime('now'));  // 220

// -- scenario 3: same as scenario 1, but revenue was created 4 months ago and is set to repeat every month

// $revenue->setDate(new DateTime('-4 months'));
// $revenue->setRepeating();
// echo $money->getBalance(new DateTime('now'));  // 5490

$revenue = new Revenue(800, 'Website development', 'Website development description', $category);
$revenue->setDate(new DateTime('+4 days'), true);
$inflow1 = new Inflow(500, new DateTime('+2 days'), $revenue, $payment1);
$inflow1->setProbability(0.80);
$inflow2 = new Inflow(300, new DateTime('+6 days'), $revenue, $payment2);
$inflow2->setProbability(0.80);

$expense = new Expense(300, 'WP theme purchase', 'WP theme purchase description', $category);
$expense->setDate(new DateTime('+4 days'), true);
$outflow1 = new Outflow(100, new DateTime('+2 days'), $expense, $payment1);
$outflow1->setProbability(0.80);
$outflow2 = new Outflow(200, new DateTime('+6 days'), $expense, $payment2);
$outflow2->setProbability(0.80);

$money = new MoneyFlow;
$money->addRevenue($revenue)->addExpense($expense);

// -- scenario 4: predictions
// echo $money->getBalancePrediction(new DateTime('+5 days')); // 280 - only inflow1 and outflow2 gets counted
// echo $money->getBalancePrediction(new DateTime('+7 days')); // 350
