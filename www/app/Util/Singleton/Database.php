<?php

namespace App\Util\Singleton;

use App\Util\Config;
use Exception;
use Mysqli;
use mysqli_stmt;

/**
 * Database wrapper.
 */
class Database
{
    /** @var Database|null Singleton instance. */
    private static ?Database $instance = null;

    /** @var Mysqli Database connection. */
    public Mysqli $con;

    /** @var string|null Stored query. */
    private ?string $query = null;

    /**
     * Returns the singleton instance.
     * @return Database
     */
    public static function instance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Sets up the Database object.
     * @throws Exception
     */
    public function __construct()
    {
        $this->con = new Mysqli(
            Config::MYSQL_HOST,
            Config::MYSQL_USER,
            Config::MYSQL_PASSWORD,
            Config::MYSQL_DB
        );
        if ($this->con->connect_error) {
            throw new Exception("Database verbinding mislukt: " . $this->con->connect_error);
        }
    }

    /**
     * Store a query (for debugging purposes).
     *
     * @param string $query
     * @return void
     */
    public function storeQuery(string $query): void
    {
        $this->query = $query;
    }

    /**
     * Direct call for preparing a stored query.
     *
     * @return false|mysqli_stmt
     */
    public function prepareStoredQuery(): false|mysqli_stmt
    {
        return $this->con->prepare($this->query);
    }

    /**
     * @return string|null The stored query.
     */
    public function getStoredQuery(): ?string
    {
        return $this->query;
    }
}
