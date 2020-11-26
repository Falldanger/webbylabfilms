<?php

namespace database;

use PDO;

class Connection
{
    const DB_WEBBYLABFILMS = 'dbname=webbylabfilms';
    private $databaseName = '';

    public function make()
    {
        return $db = new PDO("mysql:host=localhost;" . $this->databaseName, 'root', '');
    }

    public function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
    }
}
