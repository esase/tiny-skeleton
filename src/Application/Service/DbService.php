<?php

namespace Tiny\Skeleton\Application\Service;

use PDO;

class DbService
{

    /**
     * @var PDO
     */
    protected PDO $connection;

    /**
     * DbService constructor.
     *
     * @param  string  $host
     * @param  string  $userName
     * @param  string  $password
     * @param  string  $dbName
     */
    public function __construct(
        string $host,
        string $userName,
        string $password,
        string $dbName
    ) {
        $this->connection = new PDO(
            "mysql:host=$host;dbname=$dbName", $userName, $password
        );
        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

}