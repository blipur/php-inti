<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Repository;

use Imadepurnamayasa\PhpInti\Entity\Entity;

/**
 * Interface RepositoryInterface
 * Defines standard operations for domain-driven repositories.
 */
interface RepositoryInterface
{
    public function getById($id): ?Entity;
    public function getAll(): array;
    public function store(Entity $entity): Entity;
    public function remove(Entity $entity): bool;
}
