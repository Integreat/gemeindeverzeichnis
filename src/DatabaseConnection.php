<?php

namespace Integreat\Gemeindeverzeichnis;

use mysqli;

class DatabaseConnection extends mysqli
{
    public function __construct(
        $host = null,
        $username = null,
        $passwd = null,
        $dbname = null,
        $port = null,
        $socket = null
    ) {
        parent::__construct($host, $username, $passwd, $dbname, $port, $socket);
        if ($this->connect_error) {
            throw new \RuntimeException('Connection failed: ' . $this->connect_error);
        }
    }
}
