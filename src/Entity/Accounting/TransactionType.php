<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Entity\Accounting;

use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class TransactionType
 * Entity representing standardized transaction types.
 */
class TransactionType extends Entity
{
    public const TYPE_RECEIPT = 'RECEIPT';
    public const TYPE_PAYMENT = 'PAYMENT';
    public const TYPE_INVOICE = 'INVOICE';
    public const TYPE_BILL = 'BILL';
    public const TYPE_ADJUSTMENT = 'ADJUSTMENT';

    protected string $code;
    protected string $name;
    protected string $description = '';

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
