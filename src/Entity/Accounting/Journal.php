<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Entity\Accounting;

use Imadepurnamayasa\PhpInti\Entity\Entity;
use DateTime;

/**
 * Class Journal
 * Entity representing a General Journal header.
 */
class Journal extends Entity
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';
    public const STATUS_VOID = 'void';

    protected DateTime $transactionDate;
    protected string $referenceNumber;
    protected string $description;
    protected float $totalAmount = 0.0;
    protected string $status = self::STATUS_DRAFT;

    public function getTransactionDate(): DateTime
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(DateTime $transactionDate): void
    {
        $this->transactionDate = $transactionDate;
    }

    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): void
    {
        $this->referenceNumber = $referenceNumber;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
