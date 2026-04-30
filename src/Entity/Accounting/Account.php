<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Entity\Accounting;

use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class Account
 * Entity representing a Chart of Accounts (COA).
 */
class Account extends Entity
{
    public const TYPE_ASSET = 'asset';
    public const TYPE_LIABILITY = 'liability';
    public const TYPE_EQUITY = 'equity';
    public const TYPE_REVENUE = 'revenue';
    public const TYPE_EXPENSE = 'expense';

    protected string $code;
    protected string $name;
    protected string $type;
    protected float $balance = 0.0;
    protected int|string|null $reportGroupId = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function getReportGroupId(): int|string|null
    {
        return $this->reportGroupId;
    }

    public function setReportGroupId(int|string|null $reportGroupId): void
    {
        $this->reportGroupId = $reportGroupId;
    }
}
