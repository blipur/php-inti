<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Dao\Accounting;

use Imadepurnamayasa\PhpInti\Dao\AbstractDao;
use Imadepurnamayasa\PhpInti\Entity\Accounting\TransactionType;
use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class TransactionTypeDao
 * Data Access Object for the TransactionType entity.
 */
class TransactionTypeDao extends AbstractDao
{
    protected string $table = 'transaction_types';

    protected function mapRowToEntity(array $row): Entity
    {
        $type = new TransactionType();
        
        if (isset($row['id'])) {
            $type->setId($row['id']);
        }
        if (isset($row['code'])) {
            $type->setCode($row['code']);
        }
        if (isset($row['name'])) {
            $type->setName($row['name']);
        }
        if (isset($row['description'])) {
            $type->setDescription($row['description']);
        }
        
        return $type;
    }

    protected function mapEntityToRow(Entity $entity): array
    {
        /** @var TransactionType $entity */
        return [
            'code' => $entity->getCode(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
        ];
    }
}
