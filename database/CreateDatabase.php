<?php

namespace database;

include_once './Connection.php';

use database\Connection as Connection;
use PDO;
use PDOException;

try {
    $connection = new Connection();
    $databaseName = explode('dbname=', $connection::DB_WEBBYLABFILMS);
    $connection = $connection->make();
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE IF NOT EXISTS $databaseName[1]";
    $connection->exec($sql);
    $sql = "use $databaseName[1]";
    $connection->exec($sql);
    $sql = "CREATE TABLE IF NOT EXISTS films (
                id int(11) AUTO_INCREMENT PRIMARY KEY,
                film_name varchar(64) NOT NULL,
                year year(4) NOT NULL,
                format varchar(32) NOT NULL,
                actors TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
    $connection->exec($sql);
    echo "DB created successfully";
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}
