<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Dao\Accounting;

use Imadepurnamayasa\PhpInti\Dao\AbstractDao;
use Imadepurnamayasa\PhpInti\Entity\Accounting\JournalItem;
use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class JournalItemDao
 * Data Access Object for the JournalItem entity.
 */
class JournalItemDao extends AbstractDao
{
    protected string $table = 'journal_items';

    protected function mapRowToEntity(array $row): Entity
    {
        $item = new JournalItem();
        
        if (isset($row['id'])) {
            $item->setId($row['id']);
        }
        if (isset($row['journal_id'])) {
            $item->setJournalId($row['journal_id']);
        }
        if (isset($row['account_id'])) {
            $item->setAccountId($row['account_id']);
        }
        if (isset($row['debit'])) {
            $item->setDebit((float)$row['debit']);
        }
        if (isset($row['credit'])) {
            $item->setCredit((float)$row['credit']);
        }
        if (isset($row['description'])) {
            $item->setDescription($row['description']);
        }
        
        return $item;
    }

    protected function mapEntityToRow(Entity $entity): array
    {
        /** @var JournalItem $entity */
        return [
            'journal_id' => $entity->getJournalId(),
            'account_id' => $entity->getAccountId(),
            'debit' => $entity->getDebit(),
            'credit' => $entity->getCredit(),
            'description' => $entity->getDescription(),
        ];
    }
}
