<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_money_stores_in_cents(): void
    {
        $money = new Money(10050);
        $this->assertEquals(10050, $money->amount());
        $this->assertEquals(100.50, $money->toFloat());
    }

    public function test_money_addition(): void
    {
        $a = new Money(10000);
        $b = new Money(5000);
        $this->assertEquals(15000, $a->add($b)->amount());
    }

    public function test_money_multiplication(): void
    {
        $money = new Money(50000);
        $this->assertEquals(75000, $money->multiply(1.5)->amount());
    }

    public function test_negative_amount_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money(-1);
    }

    public function test_from_float(): void
    {
        $money = Money::fromFloat(99.99);
        $this->assertEquals(9999, $money->amount());
    }
}
