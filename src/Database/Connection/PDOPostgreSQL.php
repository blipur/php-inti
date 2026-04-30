<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\Connection;

class PDOPostgreSQL extends PDOConnection
{
    public function getDsn(): string
    {
        return "pgsql:host=$this->host;port=$this->port;dbname=$this->dbname";
    }
}
