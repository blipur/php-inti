<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\ActiveRecord;

use Imadepurnamayasa\PhpInti\Database\Connection\ConnectionInterface;
use PDO;
use RuntimeException;

/**
 * Class ActiveRecord
 * Abstract base class for the Active Record design pattern.
 * Allows models to directly map to database tables and perform CRUD operations.
 */
abstract class ActiveRecord
{
    /** @var ConnectionInterface|null Global database connection for all Active Record models. */
    protected static ?ConnectionInterface $connection = null;

    /** @var string The table associated with the model. Must be defined in child classes. */
    protected string $table;

    /** @var string The primary key for the model. */
    protected string $primaryKey = 'id';

    /** @var array The model's attributes (database columns). */
    protected array $attributes = [];

    /**
     * Sets the global database connection.
     *
     * @param ConnectionInterface $connection
     * @return void
     */
    public static function setConnection(ConnectionInterface $connection): void
    {
        self::$connection = $connection;
    }

    /**
     * Gets the database connection. Throws an exception if not set.
     *
     * @return ConnectionInterface
     * @throws RuntimeException
     */
    protected static function getConnection(): ConnectionInterface
    {
        if (self::$connection === null) {
            throw new RuntimeException("ActiveRecord connection has not been set. Call ActiveRecord::setConnection() first.");
        }
        return self::$connection;
    }

    /**
     * Magic method to get an attribute.
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Magic method to set an attribute.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Magic method to check if an attribute is set.
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Fills the model with an array of attributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    /**
     * Gets all attributes as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Finds a record by its primary key.
     *
     * @param mixed $id
     * @return static|null
     */
    public static function find($id): ?self
    {
        $instance = new static();
        $pdo = self::getConnection()->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = :id LIMIT 1");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        $instance->fill($row);
        return $instance;
    }

    /**
     * Retrieves all records from the table.
     *
     * @return static[]
     */
    public static function all(): array
    {
        $instance = new static();
        $pdo = self::getConnection()->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM {$instance->table}");
        $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $models = [];
        
        foreach ($rows as $row) {
            $model = new static();
            $model->fill($row);
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * Saves the model to the database (inserts or updates).
     *
     * @return bool
     */
    public function save(): bool
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return $this->update();
        }
        return $this->insert();
    }

    /**
     * Inserts the model into the database.
     *
     * @return bool
     */
    public function insert(): bool
    {
        $pdo = self::getConnection()->getConnection();
        
        $keys = array_keys($this->attributes);
        $columns = implode(', ', $keys);
        $placeholders = implode(', ', array_map(function ($key) {
            return ":$key";
        }, $keys));
        
        $stmt = $pdo->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        
        foreach ($this->attributes as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $result = $stmt->execute();
        
        if ($result) {
            $lastInsertId = $pdo->lastInsertId();
            if ($lastInsertId) {
                $this->attributes[$this->primaryKey] = is_numeric($lastInsertId) ? (int)$lastInsertId : $lastInsertId;
            }
        }
        
        return $result;
    }

    /**
     * Updates the model in the database.
     *
     * @return bool
     * @throws RuntimeException
     */
    public function update(): bool
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            throw new RuntimeException("Cannot update a model without a primary key.");
        }

        $pdo = self::getConnection()->getConnection();
        
        $updates = [];
        foreach (array_keys($this->attributes) as $key) {
            if ($key !== $this->primaryKey) {
                $updates[] = "$key = :$key";
            }
        }
        
        if (empty($updates)) {
            return true; // Nothing to update
        }
        
        $setString = implode(', ', $updates);
        $stmt = $pdo->prepare("UPDATE {$this->table} SET $setString WHERE {$this->primaryKey} = :primary_key");
        
        foreach ($this->attributes as $key => $value) {
            if ($key !== $this->primaryKey) {
                $stmt->bindValue(":$key", $value);
            }
        }
        
        $stmt->bindValue(':primary_key', $this->attributes[$this->primaryKey]);
        
        return $stmt->execute();
    }

    /**
     * Deletes the model from the database.
     *
     * @return bool
     * @throws RuntimeException
     */
    public function delete(): bool
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            throw new RuntimeException("Cannot delete a model without a primary key.");
        }

        $pdo = self::getConnection()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->bindValue(':id', $this->attributes[$this->primaryKey]);
        
        return $stmt->execute();
    }
}
