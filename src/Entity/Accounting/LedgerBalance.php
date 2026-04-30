<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Entity\Accounting;

use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class LedgerBalance
 * Entity representing the Account Balance per Period (e.g., Monthly).
 */
class LedgerBalance extends Entity
{
    protected int|string $accountId;
    protected string $period;
    protected float $openingBalance = 0.0;
    protected float $debitMutation = 0.0;
    protected float $creditMutation = 0.0;
    protected float $closingBalance = 0.0;

    public function getAccountId(): int|string
    {
        return $this->accountId;
    }

    public function setAccountId(int|string $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function getOpeningBalance(): float
    {
        return $this->openingBalance;
    }

    public function setOpeningBalance(float $openingBalance): void
    {
        $this->openingBalance = $openingBalance;
    }

    public function getDebitMutation(): float
    {
        return $this->debitMutation;
    }

    public function setDebitMutation(float $debitMutation): void
    {
        $this->debitMutation = $debitMutation;
    }

    public function getCreditMutation(): float
    {
        return $this->creditMutation;
    }

    public function setCreditMutation(float $creditMutation): void
    {
        $this->creditMutation = $creditMutation;
    }

    public function getClosingBalance(): float
    {
        return $this->closingBalance;
    }

    public function setClosingBalance(float $closingBalance): void
    {
        $this->closingBalance = $closingBalance;
    }
}
