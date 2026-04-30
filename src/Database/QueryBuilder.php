<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database;

/**
 * Class QueryBuilder
 * Utilitas untuk membangun kueri SQL (SELECT) secara terprogram (fluent interface).
 */
class QueryBuilder
{
    /** @var string Nama tabel utama untuk kueri. */
    protected $table;

    /** @var string Kolom-kolom yang akan dipilih (default: '*'). */
    protected $select = '*';

    /** @var array Daftar klausa JOIN. */
    protected $join = [];

    /** @var array Daftar klausa WHERE. */
    protected $where = [];

    /** @var array Daftar klausa GROUP BY. */
    protected $groupBy = [];

    /** @var array Daftar klausa ORDER BY. */
    protected $orderBy = [];    

    /**
     * Menentukan tabel utama untuk kueri.
     *
     * @param string $table Nama tabel.
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Menentukan kolom apa saja yang akan diambil (SELECT).
     *
     * @param array|string $columns String atau array nama kolom.
     * @return $this
     */
    public function select($columns)
    {
        if (is_array($columns)) {
            $this->select = implode(', ', $columns);
        } else {
            $this->select = $columns;
        }
        return $this;
    }

    /**
     * Menambahkan kondisi WHERE ke kueri.
     *
     * @param string $column Nama kolom.
     * @param string $operator Operator perbandingan (misal: '=', '>', '<', 'LIKE').
     * @param mixed $value Nilai yang dicari.
     * @return $this
     */
    public function where($column, $operator, $value)
    {
        $this->where[] = compact('column', 'operator', 'value');
        return $this;
    }

    /**
     * Menambahkan klausa JOIN ke kueri.
     *
     * @param string $type Tipe join (misal: 'INNER', 'LEFT', 'RIGHT').
     * @param string $table Nama tabel yang akan di-join.
     * @param string $firstColumn Kolom pertama untuk kondisi join (dari tabel sumber).
     * @param string $operator Operator perbandingan (misal: '=').
     * @param string $secondColumn Kolom kedua untuk kondisi join (dari tabel target).
     * @return $this
     */
    public function join($type, $table, $firstColumn, $operator, $secondColumn)
    {
        $this->join[] = compact('type', 'table', 'firstColumn', 'operator', 'secondColumn');
        return $this;
    }

    /**
     * Menambahkan klausa GROUP BY ke kueri.
     *
     * @param string $column Nama kolom untuk pengelompokan.
     * @return $this
     */
    public function groupBy($column)
    {
        $this->groupBy[] = compact('column');
        return $this;
    }

    /**
     * Menambahkan klausa ORDER BY ke kueri.
     *
     * @param string $column Nama kolom untuk pengurutan.
     * @param string $direction Arah pengurutan ('ASC' atau 'DESC'). Default 'ASC'.
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy[] = compact('column', 'direction');
        return $this;
    }

    /**
     * Menyusun semua konfigurasi menjadi satu string kueri SQL SELECT utuh.
     *
     * @return string Kueri SQL yang telah dibangun.
     */
    public function build()
    {
        $query = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                $query .= " {$join['type']} JOIN {$join['table']} ON {$this->table}.{$join['firstColumn']} {$join['operator']} {$join['table']}.{$join['secondColumn']}";
            }
        }

        if (!empty($this->where)) {
            $query .= " WHERE ";
            foreach ($this->where as $index => $condition) {
                if ($index > 0) {
                    $query .= " AND ";
                }
                $query .= "{$condition['column']} {$condition['operator']} '{$condition['value']}'";
            }
        }        

        if (!empty($this->groupBy)) {
            $groupClauses = [];
            foreach ($this->groupBy as $index => $column) {
                $groupClauses[] = "{$column['column']}";
            }
            $query .= " GROUP BY " . implode(', ', $groupClauses);
        }

        if (!empty($this->orderBy)) {
            $orderClauses = [];
            foreach ($this->orderBy as $index => $condition) {
                $orderClauses[] = "{$condition['column']} {$condition['direction']}";
            }
            $query .= " ORDER BY " . implode(', ', $orderClauses);
        }

        return $query;
    }
}
