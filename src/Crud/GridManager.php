<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Crud;

/**
 * Class GridManager
 * Utilitas untuk mengelola dan merender tabel/grid data dalam bentuk HTML.
 */
class GridManager
{
    /** @var array Array dua dimensi yang menyimpan data baris dan kolom. */
    private $grid;

    /** @var int Jumlah baris. */
    private $rows;

    /** @var int Jumlah kolom. */
    private $cols;

    /** @var array Array satu dimensi untuk menyimpan judul kolom (header). */
    private $headers;

    /**
     * Konstruktor GridManager.
     *
     * @param int $rows Jumlah awal baris (default 0).
     * @param int $cols Jumlah kolom.
     */
    public function __construct($rows = 0, $cols)
    {
        $this->rows = $rows;
        $this->cols = $cols;
        $this->grid = [];
        $this->headers = array_fill(0, $cols, '');
        for ($i = 0; $i < $rows; $i++) {
            $row = [];
            for ($j = 0; $j < $cols; $j++) {
                $row[] = 'col-' . $j;
            }
            $this->grid[] = $row;
        }
    }

    /**
     * Menambahkan baris kosong baru ke bagian bawah grid.
     *
     * @return void
     */
    public function addRow()
    {
        $row = [];
        for ($j = 0; $j < $this->cols; $j++) {
            $row[] = 'col-' . $j;
        }
        $this->grid[] = $row;
        $this->rows++;
    }

    /**
     * Mengatur nilai (value) untuk sel tertentu berdasarkan indeks baris dan kolom.
     *
     * @param int $row Indeks baris (mulai dari 0).
     * @param int $col Indeks kolom (mulai dari 0).
     * @param mixed $value Nilai yang akan dimasukkan ke dalam sel.
     * @return bool True jika berhasil diatur, false jika indeks tidak valid.
     */
    public function setCellValue($row, $col, $value)
    {
        if ($this->isValidCell($row, $col)) {
            $this->grid[$row][$col] = $value;
            return true;
        }
        return false;
    }

    /**
     * Mengambil nilai sel pada indeks baris dan kolom tertentu.
     *
     * @param int $row Indeks baris.
     * @param int $col Indeks kolom.
     * @return mixed|null Nilai sel atau null jika indeks tidak valid.
     */
    public function getCellValue($row, $col)
    {
        if ($this->isValidCell($row, $col)) {
            return $this->grid[$row][$col];
        }
        return null;
    }

    /**
     * Mengatur teks judul (header) untuk kolom tertentu.
     *
     * @param int $col Indeks kolom.
     * @param string $header Teks judul header.
     * @return bool True jika berhasil, false jika indeks kolom tidak valid.
     */
    public function setColumnHeader($col, $header)
    {
        if ($col >= 0 && $col < $this->cols) {
            $this->headers[$col] = $header;
            return true;
        }
        return false;
    }

    /**
     * Merender data grid menjadi sebuah elemen tabel HTML murni.
     *
     * @return string String yang berisi elemen <table> HTML.
     */
    public function renderTable()
    {
        $table = '<table border="1">';
        // Header row
        $table .= '<tr>';
        foreach ($this->headers as $header) {
            $table .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $table .= '</tr>';
        // Data rows
        for ($i = 0; $i < $this->rows; $i++) {
            $table .= '<tr>';
            for ($j = 0; $j < $this->cols; $j++) {
                $cellValue = $this->getCellValue($i, $j);
                $table .= '<td>' . ($cellValue) . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</table>';
        
        return $table;
    }

    /**
     * Memeriksa apakah indeks baris dan kolom berada dalam batas grid yang ada.
     *
     * @param int $row Indeks baris.
     * @param int $col Indeks kolom.
     * @return bool True jika valid, false sebaliknya.
     */
    private function isValidCell($row, $col)
    {
        return isset($this->grid[$row]) && isset($this->grid[$row][$col]);
    }
}
