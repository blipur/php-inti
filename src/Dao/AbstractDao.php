<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Dao;

use Imadepurnamayasa\PhpInti\Database\Connection\ConnectionInterface;
use Imadepurnamayasa\PhpInti\Entity\Entity;
use PDO;

/**
 * Class AbstractDao
 * Base Data Access Object class for managing database operations.
 */
abstract class AbstractDao implements DaoInterface
{
    protected ConnectionInterface $pdo;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct(ConnectionInterface $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Maps a database row (array) into an Entity object.
     */
    abstract protected function mapRowToEntity(array $row): Entity;

    /**
     * Maps an Entity object back into a database row (array).
     */
    abstract protected function mapEntityToRow(Entity $entity): array;

    public function findById($id): ?Entity
    {
        $stmt = $this->pdo->getConnection()->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->mapRowToEntity($row);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->getConnection()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $entities = [];
        
        foreach ($rows as $row) {
            $entities[] = $this->mapRowToEntity($row);
        }
        
        return $entities;
    }

    public function save(Entity $entity): Entity
    {
        $reflection = new \ReflectionClass($entity);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        
        if ($idProperty->isInitialized($entity) && $idProperty->getValue($entity) !== null) {
            return $this->update($entity);
        }
        
        return $this->insert($entity);
    }

    public function insert(Entity $entity): Entity
    {
        $data = $this->mapEntityToRow($entity);
        
        $keys = array_keys($data);
        $columns = implode(', ', $keys);
        $values = implode(', ', array_map(function ($key) {
            return ":$key";
        }, $keys));
        
        $stmt = $this->pdo->getConnection()->prepare("INSERT INTO {$this->table} ($columns) VALUES ($values)");
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        
        $lastInsertId = $this->pdo->getConnection()->lastInsertId();
        if ($lastInsertId) {
            $entity->setId(is_numeric($lastInsertId) ? (int)$lastInsertId : $lastInsertId);
        }
        
        return $entity;
    }

    public function update(Entity $entity): Entity
    {
        $data = $this->mapEntityToRow($entity);
        
        $reflection = new \ReflectionClass($entity);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        
        if (!$idProperty->isInitialized($entity) || $idProperty->getValue($entity) === null) {
            throw new \RuntimeException("Cannot update entity without an ID.");
        }

        $updates = '';
        foreach ($data as $key => $value) {
            if ($key !== $this->primaryKey) {
                $updates .= "$key = :$key, ";
            }
        }
        $updates = rtrim($updates, ', ');
        
        $stmt = $this->pdo->getConnection()->prepare("UPDATE {$this->table} SET $updates WHERE {$this->primaryKey} = :primary_key");
        
        foreach ($data as $key => $value) {
            if ($key !== $this->primaryKey) {
                $stmt->bindValue(":$key", $value);
            }
        }
        $stmt->bindValue(':primary_key', $idProperty->getValue($entity));
        $stmt->execute();
        
        return $entity;
    }

    public function delete(Entity $entity): bool
    {
        $reflection = new \ReflectionClass($entity);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        
        if (!$idProperty->isInitialized($entity) || $idProperty->getValue($entity) === null) {
            return false;
        }
        
        $stmt = $this->pdo->getConnection()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->bindValue(':id', $idProperty->getValue($entity));
        return $stmt->execute();
    }
}
