<?php

namespace Util\Singleton;
use Util\Singleton\Database;

/**
 * Model voor de single variables.
 */
class Configuration
{
    /** @var Configuration|null Singleton instance. */
    private static ?Configuration $instance = null;

    /** @var string */
    public string $schrijfregels;

    /** @var int */
    public int $min_checks;

    /** @var string|null */
    public string|null $mail_address;

    /** @var (string|null)[] */
    public array $passwords = [];

    /**
     * Returns the singleton instance.
     * @return Configuration
     */
    public static function instance(): Configuration
    {
        if (self::$instance === null) {
            self::$instance = new Configuration();
        }
        return self::$instance;
    }

    public function __construct()
    {
        Database::instance()->storeQuery("SELECT * FROM configuration WHERE id = 1");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $variables = $stmt->get_result()->fetch_assoc();
        if($variables) {
            $this->schrijfregels = $variables["schrijfregels"];
            $this->min_checks = $variables["min_checks"];
            $this->mail_address= $variables["mail_address"];
            $this->passwords = [null, $variables["password_1"], $variables["password_2"], $variables["password_3"]];
        }
    }


    /**
     * @param $schrijfregels
     * @param $min_checks
     * @param $mail_address
     * @param $passwords
     * @return Configuration
     */
    public function update(string $schrijfregels, int $min_checks, string|null $mail_address, array $passwords): Configuration
    {
        $this->schrijfregels = $schrijfregels;
        $this->min_checks = $min_checks;
        $this->mail_address = $mail_address;
        $this->passwords = $passwords;
        Database::instance()->storeQuery("UPDATE configuration SET schrijfregels = ?, min_checks = ?, mail_address = ?, password_1 = ?, password_2 = ?, password_3 = ? WHERE id = 1");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'sissss',
            $schrijfregels,
            $min_checks,
            $mail_address,
            $passwords[1],
            $passwords[2],
            $passwords[3]
        );
        $stmt->execute();

        return $this;
    }
}