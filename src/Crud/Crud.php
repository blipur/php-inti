<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Crud;

use Imadepurnamayasa\PhpInti\Database\Connection\ConnectionInterface;

/**
 * Class Crud
 * Base abstract class implementing the CrudInterface. 
 * Provides the foundational framework for managing tables, columns, and data types for CRUD operations.
 */
abstract class Crud implements CrudInterface
{    
    /** @var ConnectionInterface Database connection object. */
    protected ConnectionInterface $pdo;

    /** @var string Name of the database table to manage. */
    protected string $table = '';

    /** @var array List of primary key columns (supports composite keys). */
    protected array $primaryKeys = [];

    /** @var array Mapping of column names to their data types. */
    protected array $columnTypes = [];

    /** @var array List of hidden columns (not displayed in UI/Grid). */
    protected array $hideColumns = [];

    /**
     * Crud constructor.
     *
     * @param ConnectionInterface $pdo Database connection object.
     */
    public function __construct(ConnectionInterface $pdo)
    {
        $this->pdo = $pdo;      
    }

    /**
     * Sets the database table name.
     *
     * @param string $table Table name.
     * @return void
     */
    public function table(string $table)
    {
        $this->table = $table;
    }

    /**
     * Defines the primary key columns.
     *
     * @param array $columns Array of primary key column names.
     * @return void
     */
    public function primaryKeys(array $columns)
    {
        $this->primaryKeys = $columns;
    }

    /**
     * Defines data types for specific columns (e.g., for form casting).
     *
     * @param array $columns Mapping of column names to types (e.g., ['id' => 'int', 'name' => 'string']).
     * @return void
     */
    public function columnTypes(array $columns)
    {
        $this->columnTypes = $columns;
    }

    /**
     * Defines the list of columns to be hidden from views.
     *
     * @param array $columns Array of column names to hide.
     * @return void
     */
    public function hideColumns(array $columns)
    {
        $this->hideColumns = $columns;
    }
}