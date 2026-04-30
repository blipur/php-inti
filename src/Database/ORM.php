<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database;

use Imadepurnamayasa\PhpInti\Database\Connection\PDOConnection;
use PDO;

/**
 * Class ORM
 * Kelas abstrak Object-Relational Mapping (ORM) sederhana untuk berinteraksi dengan basis data.
 */
abstract class ORM
{
    /** @var PDOConnection Objek koneksi PDO. */
    protected $pdo;

    /** @var string Nama tabel basis data. */
    protected $table;

    /** @var string Nama kolom primary key. */
    protected $primaryKey;

    /**
     * Konstruktor ORM.
     *
     * @param PDOConnection $pdo Objek koneksi basis data.
     */
    public function __construct(PDOConnection $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Mencari satu baris data berdasarkan ID primary key.
     *
     * @param mixed $id Nilai primary key.
     * @return array|false Data baris berupa array asosiatif jika ditemukan, false jika tidak.
     */
    public function findById($id)
    {
        $stmt = $this->pdo->getConnection()->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil seluruh data dari tabel.
     *
     * @return array Kumpulan data baris (array asosiatif).
     */
    public function findAll()
    {
        $stmt = $this->pdo->getConnection()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menyimpan data baru ke dalam tabel.
     *
     * @param array $data Data yang akan disimpan dengan format kunci-nilai (kolom-nilai).
     * @return string|false ID dari data yang baru saja disisipkan.
     */
    public function create($data)
    {
        $keys = array_keys($data);
        $columns = implode(',', $keys);
        $values = implode(',', array_map(function ($key) {
            return ":$key";
        }, $keys));

        $stmt = $this->pdo->getConnection()->prepare("INSERT INTO {$this->table} ($columns) VALUES ($values)");
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        return $this->pdo->getConnection()->lastInsertId();
    }

    /**
     * Memperbarui data yang ada di tabel berdasarkan ID.
     *
     * @param mixed $id Nilai primary key yang akan diperbarui.
     * @param array $data Data baru dengan format kunci-nilai (kolom-nilai).
     * @return bool True jika berhasil, false jika gagal.
     */
    public function update($id, $data)
    {
        $updates = '';
        foreach ($data as $key => $value) {
            $updates .= "$key = :$key,";
        }
        $updates = rtrim($updates, ',');

        $stmt = $this->pdo->getConnection()->prepare("UPDATE {$this->table} SET $updates WHERE {$this->primaryKey} = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        return $stmt->execute();
    }

    /**
     * Menghapus data dari tabel berdasarkan ID.
     *
     * @param mixed $id Nilai primary key yang akan dihapus.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function delete($id)
    {
        $stmt = $this->pdo->getConnection()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Mencari data berdasarkan kondisi tertentu.
     *
     * @param array $conditions Array berisi string kondisi (misal: ['status = :status']).
     * @param array $values Array asosiatif nilai yang akan di-bind (misal: [':status' => 'aktif']).
     * @return array Kumpulan data baris (array asosiatif).
     */
    public function where($conditions, $values = [])
    {
        $where = implode(' AND ', $conditions);
        $stmt = $this->pdo->getConnection()->prepare("SELECT * FROM {$this->table} WHERE $where");
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menjalankan query SQL bebas (raw SQL) dan mengembalikan banyak baris.
     *
     * @param string $sql Kueri SQL yang akan dieksekusi.
     * @param array $values Array asosiatif parameter yang akan di-bind.
     * @return array Kumpulan data baris (array asosiatif).
     */
    public function queryAll($sql, $values = [])
    {
        $stmt = $this->pdo->getConnection()->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menjalankan query SQL bebas (raw SQL) dan mengembalikan satu baris saja.
     *
     * @param string $sql Kueri SQL yang akan dieksekusi.
     * @param array $values Array asosiatif parameter yang akan di-bind.
     * @return array|false Data baris berupa array asosiatif jika ditemukan, false jika tidak.
     */
    public function queryOne($sql, $values = [])
    {
        $stmt = $this->pdo->getConnection()->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
