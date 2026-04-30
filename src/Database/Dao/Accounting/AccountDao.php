<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\Dao\Accounting;

use Imadepurnamayasa\PhpInti\Database\Dao\AbstractDao;
use Imadepurnamayasa\PhpInti\Entity\Accounting\Account;
use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class AccountDao
 * Data Access Object for the Account entity.
 */
class AccountDao extends AbstractDao
{
    protected string $table = 'accounts';

    protected function mapRowToEntity(array $row): Entity
    {
        $account = new Account();
        
        if (isset($row['id'])) {
            $account->setId($row['id']);
        }
        if (isset($row['code'])) {
            $account->setCode($row['code']);
        }
        if (isset($row['name'])) {
            $account->setName($row['name']);
        }
        if (isset($row['type'])) {
            $account->setType($row['type']);
        }
        if (isset($row['balance'])) {
            $account->setBalance((float)$row['balance']);
        }
        if (isset($row['report_group_id'])) {
            $account->setReportGroupId($row['report_group_id']);
        }
        
        return $account;
    }

    protected function mapEntityToRow(Entity $entity): array
    {
        /** @var Account $entity */
        return [
            'code' => $entity->getCode(),
            'name' => $entity->getName(),
            'type' => $entity->getType(),
            'balance' => $entity->getBalance(),
            'report_group_id' => $entity->getReportGroupId(),
        ];
    }
}
