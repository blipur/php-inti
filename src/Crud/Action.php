<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Crud;

use DateTime;
use Exception;
use Imadepurnamayasa\PhpInti\Helpers;

class Action extends Crud
{
    public function process(string $action = '')
    {
        if ($action === 'insert') {
            return $this->insert();
        } else if ($action === 'update') {
            return $this->update();
        } else if ($action === 'delete') {
            return $this->delete();
        } else {
            return json_encode([
                'messages' => 'Action not available.'
            ]);
        }
    }

    public function insert()
    {
        if (!isset($_POST['crudform'])) {
            return json_encode([
                'messages' => "Form data not available."
            ]);
        }

        $data = $_POST['crudform'];

        Helpers::print_r($data);

        foreach ($data as $key => $value) {
            if (in_array($key, $this->primaryKeys)) {
                unset($data[$key]);
            }
        }

        $sql = "INSERT INTO $this->table (";
        $sql .= implode(", ", array_keys($data));
        $sql .= ") VALUES (";
        $sql .= ":" . implode(", :", array_keys($data));
        $sql .= ")";

        $stmt = $this->pdo->getConnection()->prepare($sql);

        foreach ($data as $key => $value) {
            if (in_array($key, array_keys($this->columnTypes))) {
                if ($this->columnTypes[$key] === 'DATETIME') {
                    $dateTime = DateTime::createFromFormat('d-m-Y H:i:s', $value);
                    if ($dateTime instanceof DateTime) {
                        $value = $dateTime->format('Y-m-d H:i:s');
                    } else {
                        $currentDateTime = new DateTime();
                        $value = $currentDateTime->format('Y-m-d H:i:s');
                    }
                }
            }
            $stmt->bindValue(":$key", $value);
        }

        try {
            $stmt->execute();
            return json_encode([
                'messages' => "Record inserted successfully."
            ]);
        } catch (Exception $e) {
            return json_encode([
                'messages' => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        if (!isset($_POST['crudform'])) {
            return json_encode([
                'messages' => "Form data not available."
            ]);
        }

        $data = $_POST['crudform'];
        
        $whereClauses = [];
        $whereValues = [];
        foreach ($this->primaryKeys as $pk) {
            if (!isset($data[$pk])) {
                 return json_encode(['messages' => "Primary key $pk is missing."]);
            }
            $whereClauses[] = "$pk = :pk_$pk";
            $whereValues[":pk_$pk"] = $data[$pk];
            unset($data[$pk]); // Don't update primary keys
        }

        if (empty($data)) {
            return json_encode(['messages' => "No data to update."]);
        }

        $setClauses = [];
        foreach (array_keys($data) as $key) {
            $setClauses[] = "$key = :$key";
        }

        $sql = "UPDATE {$this->table} SET " . implode(", ", $setClauses) . " WHERE " . implode(" AND ", $whereClauses);
        
        $stmt = $this->pdo->getConnection()->prepare($sql);
        
        foreach ($whereValues as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        foreach ($data as $key => $value) {
            if (in_array($key, array_keys($this->columnTypes))) {
                if ($this->columnTypes[$key] === 'DATETIME') {
                    $dateTime = DateTime::createFromFormat('d-m-Y H:i:s', $value);
                    if ($dateTime instanceof DateTime) {
                        $value = $dateTime->format('Y-m-d H:i:s');
                    } else {
                        $currentDateTime = new DateTime();
                        $value = $currentDateTime->format('Y-m-d H:i:s');
                    }
                }
            }
            $stmt->bindValue(":$key", $value);
        }

        try {
            $stmt->execute();
            return json_encode([
                'messages' => "Record updated successfully."
            ]);
        } catch (Exception $e) {
            return json_encode([
                'messages' => $e->getMessage()
            ]);
        }
    }

    public function delete()
    {
        if (!isset($_POST['crudform'])) {
            return json_encode([
                'messages' => "Form data not available."
            ]);
        }

        $data = $_POST['crudform'];
        
        $whereClauses = [];
        $whereValues = [];
        foreach ($this->primaryKeys as $pk) {
            if (!isset($data[$pk])) {
                 return json_encode(['messages' => "Primary key $pk is missing."]);
            }
            $whereClauses[] = "$pk = :pk_$pk";
            $whereValues[":pk_$pk"] = $data[$pk];
        }

        $sql = "DELETE FROM {$this->table} WHERE " . implode(" AND ", $whereClauses);
        
        $stmt = $this->pdo->getConnection()->prepare($sql);
        
        foreach ($whereValues as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        try {
            $stmt->execute();
            return json_encode([
                'messages' => "Record deleted successfully."
            ]);
        } catch (Exception $e) {
            return json_encode([
                'messages' => $e->getMessage()
            ]);
        }
    }
}
