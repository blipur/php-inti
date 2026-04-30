<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Crud;

/**
 * Class GridManager
 * Utility for managing and rendering data grids/tables in HTML format.
 */
class GridManager
{
    /** @var array Two-dimensional array storing row and column data. */
    private $grid;

    /** @var int Number of rows. */
    private $rows;

    /** @var int Number of columns. */
    private $cols;

    /** @var array One-dimensional array storing column headers. */
    private $headers;

    /**
     * GridManager constructor.
     *
     * @param int $rows Initial number of rows (default 0).
     * @param int $cols Number of columns.
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
     * Adds a new empty row to the bottom of the grid.
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
     * Sets the value for a specific cell based on row and column index.
     *
     * @param int $row Row index (starts from 0).
     * @param int $col Column index (starts from 0).
     * @param mixed $value The value to insert into the cell.
     * @return bool True if set successfully, false if index is invalid.
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
     * Retrieves the cell value at a specific row and column index.
     *
     * @param int $row Row index.
     * @param int $col Column index.
     * @return mixed|null Cell value or null if index is invalid.
     */
    public function getCellValue($row, $col)
    {
        if ($this->isValidCell($row, $col)) {
            return $this->grid[$row][$col];
        }
        return null;
    }

    /**
     * Sets the header text for a specific column.
     *
     * @param int $col Column index.
     * @param string $header Header text.
     * @return bool True if successful, false if column index is invalid.
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
     * Renders the grid data into a pure HTML table element.
     *
     * @return string String containing the HTML <table> element.
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
     * Checks whether the row and column indices are within the existing grid bounds.
     *
     * @param int $row Row index.
     * @param int $col Column index.
     * @return bool True if valid, false otherwise.
     */
    private function isValidCell($row, $col)
    {
        return isset($this->grid[$row]) && isset($this->grid[$row][$col]);
    }
}
