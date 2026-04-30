<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\Dao;

use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Interface DaoInterface
 * Defines standard Data Access Object operations.
 */
interface DaoInterface
{
    public function findById($id): ?Entity;
    public function findAll(): array;
    public function save(Entity $entity): Entity;
    public function insert(Entity $entity): Entity;
    public function update(Entity $entity): Entity;
    public function delete(Entity $entity): bool;
}
