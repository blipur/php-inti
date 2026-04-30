<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Dao\Accounting;

use Imadepurnamayasa\PhpInti\Dao\AbstractDao;
use Imadepurnamayasa\PhpInti\Entity\Accounting\ReportGroup;
use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class ReportGroupDao
 * Data Access Object for the ReportGroup entity.
 */
class ReportGroupDao extends AbstractDao
{
    protected string $table = 'report_groups';

    protected function mapRowToEntity(array $row): Entity
    {
        $group = new ReportGroup();
        
        if (isset($row['id'])) {
            $group->setId($row['id']);
        }
        if (isset($row['code'])) {
            $group->setCode($row['code']);
        }
        if (isset($row['name'])) {
            $group->setName($row['name']);
        }
        if (isset($row['report_type'])) {
            $group->setReportType($row['report_type']);
        }
        if (isset($row['parent_id'])) {
            $group->setParentId($row['parent_id']);
        }
        if (isset($row['sort_order'])) {
            $group->setSortOrder((int)$row['sort_order']);
        }
        
        return $group;
    }

    protected function mapEntityToRow(Entity $entity): array
    {
        /** @var ReportGroup $entity */
        return [
            'code' => $entity->getCode(),
            'name' => $entity->getName(),
            'report_type' => $entity->getReportType(),
            'parent_id' => $entity->getParentId(),
            'sort_order' => $entity->getSortOrder(),
        ];
    }
}
