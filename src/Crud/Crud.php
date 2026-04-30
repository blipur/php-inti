<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Crud;

use Imadepurnamayasa\PhpInti\Database\Connection\ConnectionInterface;

/**
 * Class Crud
 * Kelas abstrak dasar yang mengimplementasikan antarmuka CrudInterface. 
 * Menyediakan kerangka dasar pengaturan tabel, kolom, dan tipe data untuk operasi CRUD.
 */
abstract class Crud implements CrudInterface
{    
    /** @var ConnectionInterface Objek koneksi basis data. */
    protected ConnectionInterface $pdo;

    /** @var string Nama tabel basis data yang akan dikelola. */
    protected string $table = '';

    /** @var array Daftar primary key tabel (bisa lebih dari satu kolom untuk composite key). */
    protected array $primaryKeys = [];

    /** @var array Daftar pemetaan nama kolom dengan tipe datanya. */
    protected array $columnTypes = [];

    /** @var array Daftar kolom yang disembunyikan (tidak ditampilkan di UI/Grid). */
    protected array $hideColumns = [];

    /**
     * Konstruktor Crud.
     *
     * @param ConnectionInterface $pdo Objek koneksi basis data.
     */
    public function __construct(ConnectionInterface $pdo)
    {
        $this->pdo = $pdo;      
    }

    /**
     * Menentukan nama tabel basis data.
     *
     * @param string $table Nama tabel.
     * @return void
     */
    public function table(string $table)
    {
        $this->table = $table;
    }

    /**
     * Menentukan kolom mana saja yang menjadi primary key.
     *
     * @param array $columns Array berisi nama-nama kolom primary key.
     * @return void
     */
    public function primaryKeys(array $columns)
    {
        $this->primaryKeys = $columns;
    }

    /**
     * Mendefinisikan tipe data untuk kolom-kolom tertentu (misal untuk casting form).
     *
     * @param array $columns Array pemetaan nama kolom ke tipe (misal: ['id' => 'int', 'nama' => 'string']).
     * @return void
     */
    public function columnTypes(array $columns)
    {
        $this->columnTypes = $columns;
    }

    /**
     * Menentukan daftar kolom yang harus disembunyikan dari tampilan.
     *
     * @param array $columns Array berisi nama-nama kolom yang akan disembunyikan.
     * @return void
     */
    public function hideColumns(array $columns)
    {
        $this->hideColumns = $columns;
    }
}