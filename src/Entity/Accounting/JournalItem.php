<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Entity\Accounting;

use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class JournalItem
 * Entity representing a Journal entry line item (Debit/Credit).
 */
class JournalItem extends Entity
{
    public const TYPE_DEBIT = 'debit';
    public const TYPE_CREDIT = 'credit';

    protected int|string $journalId;
    protected int|string $accountId;
    protected float $debit = 0.0;
    protected float $credit = 0.0;
    protected string $description = '';

    public function getJournalId(): int|string
    {
        return $this->journalId;
    }

    public function setJournalId(int|string $journalId): void
    {
        $this->journalId = $journalId;
    }

    public function getAccountId(): int|string
    {
        return $this->accountId;
    }

    public function setAccountId(int|string $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function getDebit(): float
    {
        return $this->debit;
    }

    public function setDebit(float $debit): void
    {
        $this->debit = $debit;
    }

    public function getCredit(): float
    {
        return $this->credit;
    }

    public function setCredit(float $credit): void
    {
        $this->credit = $credit;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
