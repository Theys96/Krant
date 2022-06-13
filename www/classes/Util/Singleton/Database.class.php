<?php
namespace Util\Singleton;

use Exception;
use Mysqli;
use Util\Config;

/**
 * Database wrapper.
 */
class Database
{
	/** @var Database|null Singleton instance. */
	private static ?Database $instance = null;

    /** @var Mysqli Database connection. */
    public Mysqli $con;

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
}
