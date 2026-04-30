<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Crud;

use Imadepurnamayasa\PhpInti\Html\Tag\Button;
use Imadepurnamayasa\PhpInti\Html\Tag\Div;
use Imadepurnamayasa\PhpInti\Html\Tag\Form as HtmlForm;
use Imadepurnamayasa\PhpInti\Html\Tag\Input;
use Imadepurnamayasa\PhpInti\Html\Tag\Label;
use Imadepurnamayasa\PhpInti\Html\Tag\TextArea;
use PDO;

/**
 * Class Form
 * Kelas turunan Crud untuk membangun antarmuka form HTML secara dinamis berdasarkan metadata tabel basis data.
 */
class Form extends Crud
{
    /**
     * Memproses permintaan dan merender elemen form HTML berdasarkan struktur tabel.
     * Secara otomatis membaca parameter GET untuk menentukan mode (insert/update) dan mengambil data yang sesuai.
     *
     * @param string $action Aksi spesifik form (default: kosong).
     * @return string HTML yang berisi elemen <form> dan input-input di dalamnya.
     */
    public function process(string $action = '')
    {
        $get = $_GET;

        $whereClause = '';
        $whereValues = [];

        foreach ($get as $key => $value) {
            if (in_array($key, $this->primaryKeys)) {
                $whereClause .= "$key = :$key AND ";
                $whereValues[$key] = $value;
            }
        }

        if (empty($whereClause)) {
            foreach ($this->primaryKeys as $key) {
                $whereClause .= "$key = :$key AND ";
                $whereValues[$key] = '';
            }
        }

        $whereClause = rtrim($whereClause, 'AND ');

        $sql = "SELECT * FROM $this->table WHERE $whereClause LIMIT 1";

        $stmt = $this->pdo->getConnection()->prepare($sql);

        foreach ($whereValues as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        $meta = [];

        foreach (range(0, $stmt->columnCount() - 1) as $column_index) {
            $meta[] = $stmt->getColumnMeta($column_index);
        }

        $html = '';
        $form = new HtmlForm();
        $form->setId('form');
        $form->addAttribute('action', 'sakila_action.php?action=insert');
        $form->addAttribute('method', 'post');

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        foreach ($meta as $rowIndex => $row) {
            $value = isset($data[$row['name']]) ? $data[$row['name']] : '';

            if (!in_array($row['name'], $this->hideColumns)) {
                $div = new Div('');
                $div->setId("div{$row['name']}$rowIndex");

                $label = new Label('label',$row['name']);
                $label->setId("label{$row['name']}$rowIndex");
                $label->addAttribute('for', "input{$row['name']}$rowIndex");

                if ($row['pdo_type'] === PDO::PARAM_INT) {
                    if (in_array($row['native_type'], ['SHORT'])) {
                        $input = new Input('number');
                        $input->setId("input{$row['name']}$rowIndex");
                        $input->addAttribute('name', 'crudform[' . $row['name'] . ']');
                        $input->addAttribute('value', $value);
                        $input->addAttribute('min', '0');
                    } else if (in_array($row['native_type'], ['TINYINT'])) {
                        $input = new Input('number');
                        $input->setId("input{$row['name']}$rowIndex");
                        $input->addAttribute('name', 'crudform[' . $row['name'] . ']');
                        $input->addAttribute('value', $value);
                        $input->addAttribute('min', '0');
                    } else {
                        $input = new Input('number');
                        $input->setId("input{$row['name']}$rowIndex");
                        $input->addAttribute('name', 'crudform[' . $row['name'] . ']');
                        $input->addAttribute('value', $value);
                        $input->addAttribute('min', '0');
                    }
                } else if ($row['pdo_type'] === PDO::PARAM_STR) {
                    if (in_array($row['native_type'], ['STRING'])) {
                        $input = new Input('text');
                        $input->setId("input{$row['name']}$rowIndex");
                        $input->addAttribute('name', 'crudform[' . $row['name'] . ']');
                        $input->addAttribute('value', $value);
                        $input->addAttribute('size', '50');
                    } else if (in_array($row['native_type'], ['BLOB'])) {
                        $input = new TextArea('textarea', $value);
                        $input->setId("input{$row['name']}$rowIndex");
                        $input->addAttribute('cols', '50');
                        $input->addAttribute('rows', '10');
                    } else if (in_array($row['native_type'], ['DATETIME', 'TIMESTAMP'])) {
                        $input = new Input('datetime-local');
                        $input->setId("input{$row['name']}$rowIndex");
                        $input->addAttribute('name', 'crudform[' . $row['name'] . ']');
                        $input->addAttribute('value', $value);
                        $input->addAttribute('size', '50');
                    } else if (in_array($row['native_type'], ['NEWDECIMAL'])) {
                        $input = new Input('number');
                        $input->setId("input{$row['name']}$rowIndex");
                        $input->addAttribute('name', 'crudform[' . $row['name'] . ']');
                        $input->addAttribute('value', $value);
                        $input->addAttribute('min', '0');
                        $input->addAttribute('step', '.01');
                        $input->addAttribute('size', '50');
                    } else {
                        $input = new Input('text');
                        $input->setId("input{$row['name']}$rowIndex");
                        $input->addAttribute('name', 'crudform[' . $row['name'] . ']');
                        $input->addAttribute('value', $value);
                        $input->addAttribute('size', '50');
                    }
                }

                $div->addElement($label);
                $div->addElement($input);



                $form->addElement($div);                
            }
        }

        $button = new Button('submit', 'Save');
        $button->setId('btnsave');

        $form->addElement($button);

        $html .= $form->render();

        return $html;
    }
}
