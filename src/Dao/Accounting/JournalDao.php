<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Dao\Accounting;

use Imadepurnamayasa\PhpInti\Dao\AbstractDao;
use Imadepurnamayasa\PhpInti\Entity\Accounting\Journal;
use Imadepurnamayasa\PhpInti\Entity\Entity;
use DateTime;

/**
 * Class JournalDao
 * Data Access Object for the Journal entity.
 */
class JournalDao extends AbstractDao
{
    protected string $table = 'journals';

    protected function mapRowToEntity(array $row): Entity
    {
        $journal = new Journal();
        
        if (isset($row['id'])) {
            $journal->setId($row['id']);
        }
        if (isset($row['transaction_date'])) {
            $journal->setTransactionDate(new DateTime($row['transaction_date']));
        }
        if (isset($row['reference_number'])) {
            $journal->setReferenceNumber($row['reference_number']);
        }
        if (isset($row['description'])) {
            $journal->setDescription($row['description']);
        }
        if (isset($row['total_amount'])) {
            $journal->setTotalAmount((float)$row['total_amount']);
        }
        if (isset($row['status'])) {
            $journal->setStatus($row['status']);
        }
        
        return $journal;
    }

    protected function mapEntityToRow(Entity $entity): array
    {
        /** @var Journal $entity */
        return [
            'transaction_date' => $entity->getTransactionDate()->format('Y-m-d H:i:s'),
            'reference_number' => $entity->getReferenceNumber(),
            'description' => $entity->getDescription(),
            'total_amount' => $entity->getTotalAmount(),
            'status' => $entity->getStatus(),
        ];
    }
}
