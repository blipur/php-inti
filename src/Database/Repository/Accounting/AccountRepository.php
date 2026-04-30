<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\Repository\Accounting;

use Imadepurnamayasa\PhpInti\Database\Dao\Accounting\AccountDao;
use Imadepurnamayasa\PhpInti\Entity\Accounting\Account;
use Imadepurnamayasa\PhpInti\Database\Repository\AbstractRepository;

/**
 * Class AccountRepository
 * Repository for managing Account entities.
 */
class AccountRepository extends AbstractRepository
{
    /**
     * AccountRepository constructor.
     *
     * @param AccountDao $dao Specifically injects the AccountDao.
     */
    public function __construct(AccountDao $dao)
    {
        parent::__construct($dao);
    }

    /**
     * Custom domain method: Retrieves all accounts of a specific type.
     *
     * @param string $type The account type (e.g., Account::TYPE_ASSET)
     * @return Account[] Array of filtered Account entities.
     */
    public function getByType(string $type): array
    {
        // For demonstration: Since the base DAO currently doesn't expose a dynamic 'where' clause,
        // we retrieve all and filter. In a real application, you would add a findBy() method to the DAO
        // or inject a QueryBuilder into the repository to execute specialized database queries directly.
        
        $allAccounts = $this->getAll();
        $filtered = [];
        
        /** @var Account $account */
        foreach ($allAccounts as $account) {
            if ($account->getType() === $type) {
                $filtered[] = $account;
            }
        }
        
        return $filtered;
    }
}
