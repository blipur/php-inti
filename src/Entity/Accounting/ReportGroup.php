<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Entity\Accounting;

use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class ReportGroup
 * Entity representing Financial Report Groupings.
 */
class ReportGroup extends Entity
{
    public const TYPE_BALANCE_SHEET = 'BALANCE_SHEET';
    public const TYPE_INCOME_STATEMENT = 'INCOME_STATEMENT';

    protected string $code;
    protected string $name;
    protected string $reportType;
    protected int|string|null $parentId = null;
    protected int $sortOrder = 0;

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

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function setReportType(string $reportType): void
    {
        $this->reportType = $reportType;
    }

    public function getParentId(): int|string|null
    {
        return $this->parentId;
    }

    public function setParentId(int|string|null $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }
}
