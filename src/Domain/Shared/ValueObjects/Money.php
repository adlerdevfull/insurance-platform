<?php

declare(strict_types=1);

namespace Domain\Shared\ValueObjects;

final class Money
{
    public function __construct(
        private readonly int $amount, // em centavos
        private readonly string $currency = 'EUR'
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function amount(): int { return $this->amount; }
    public function currency(): string { return $this->currency; }
    public function toFloat(): float { return $this->amount / 100; }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self((int) round($this->amount * $factor), $this->currency);
    }

    public static function fromFloat(float $value, string $currency = 'EUR'): self
    {
        return new self((int) round($value * 100), $currency);
    }
}
