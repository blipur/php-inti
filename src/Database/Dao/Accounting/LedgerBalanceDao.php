<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\Dao\Accounting;

use Imadepurnamayasa\PhpInti\Database\Dao\AbstractDao;
use Imadepurnamayasa\PhpInti\Entity\Accounting\LedgerBalance;
use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class LedgerBalanceDao
 * Data Access Object for the LedgerBalance entity.
 */
class LedgerBalanceDao extends AbstractDao
{
    protected string $table = 'ledger_balances';

    protected function mapRowToEntity(array $row): Entity
    {
        $balance = new LedgerBalance();
        
        if (isset($row['id'])) {
            $balance->setId($row['id']);
        }
        if (isset($row['account_id'])) {
            $balance->setAccountId($row['account_id']);
        }
        if (isset($row['period'])) {
            $balance->setPeriod($row['period']);
        }
        if (isset($row['opening_balance'])) {
            $balance->setOpeningBalance((float)$row['opening_balance']);
        }
        if (isset($row['debit_mutation'])) {
            $balance->setDebitMutation((float)$row['debit_mutation']);
        }
        if (isset($row['credit_mutation'])) {
            $balance->setCreditMutation((float)$row['credit_mutation']);
        }
        if (isset($row['closing_balance'])) {
            $balance->setClosingBalance((float)$row['closing_balance']);
        }
        
        return $balance;
    }

    protected function mapEntityToRow(Entity $entity): array
    {
        /** @var LedgerBalance $entity */
        return [
            'account_id' => $entity->getAccountId(),
            'period' => $entity->getPeriod(),
            'opening_balance' => $entity->getOpeningBalance(),
            'debit_mutation' => $entity->getDebitMutation(),
            'credit_mutation' => $entity->getCreditMutation(),
            'closing_balance' => $entity->getClosingBalance(),
        ];
    }
}
