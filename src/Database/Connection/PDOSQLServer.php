<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\Connection;

class PDOSQLServer extends PDOConnection
{
    public function getDsn(): string
    {
        return "sqlsrv:Server=$this->host,$this->port;Database=$this->dbname";
    }
}
