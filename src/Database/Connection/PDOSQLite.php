<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\Connection;

class PDOSQLite extends PDOConnection
{
    public function getDsn(): string
    {
        // Untuk SQLite, host dapat digunakan sebagai path ke file database.
        return "sqlite:$this->host";
    }
}
