<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Repository;

use Imadepurnamayasa\PhpInti\Dao\DaoInterface;
use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Class AbstractRepository
 * Base class for all repositories. It delegates basic persistence operations
 * to the underlying Data Access Object (DAO).
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /** @var DaoInterface The underlying Data Access Object. */
    protected DaoInterface $dao;

    /**
     * AbstractRepository constructor.
     *
     * @param DaoInterface $dao The DAO to inject.
     */
    public function __construct(DaoInterface $dao)
    {
        $this->dao = $dao;
    }

    public function getById($id): ?Entity
    {
        return $this->dao->findById($id);
    }

    public function getAll(): array
    {
        return $this->dao->findAll();
    }

    public function store(Entity $entity): Entity
    {
        return $this->dao->save($entity);
    }

    public function remove(Entity $entity): bool
    {
        return $this->dao->delete($entity);
    }
}
