<?php

namespace Util\Singleton;

use Util\Singleton\Database;

/**
 * Model voor de configuratie variabelen.
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
    public array $passwords;

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
        if ($variables) {
            $this->schrijfregels = $this->getValue("schrijfregels");
            $this->min_checks = (int)$this->getValue("min_checks");
            $this->mail_address = $this->getValue("mail_address");
            $this->passwords = explode(",", $this->getValue("passwords"));
            $this->passwords[1] = $this->passwords[1] == "" ? null : $this->passwords[1];
            $this->passwords[2] = $this->passwords[2] == "" ? null : $this->passwords[2];
            $this->passwords[3] = $this->passwords[3] == "" ? null : $this->passwords[3];
        }
    }

    /**
     * @param $name
     * @return string|null
     * */
    private function getValue($name): string|null
    {
        Database::instance()->storeQuery("SELECT * FROM configuration WHERE name = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            's',
            $name
        );
        $stmt->execute();
        $config_data = $stmt->get_result()->fetch_assoc();
        return $config_data ? $config_data['value'] : null;
    }

    /**
     * @param string $schrijfregels
     * @param int $min_checks
     * @param string|null $mail_address
     * @param array $passwords
     * @return Configuration
     */
    public function updateAll(string $schrijfregels, int $min_checks, string|null $mail_address, array $passwords): Configuration
    {
        $this->schrijfregels = $this->update("schrijfregels", $schrijfregels);
        $this->min_checks = (int)$this->update("min_checks", $min_checks);
        $this->mail_address = $this->update("mail_address", $mail_address);
        $this->passwords = explode(",", $this->update("passwords", implode(",", array: $passwords)));
        $this->passwords[0] = $this->passwords[0] == "" ? null : $this->passwords[0];
        $this->passwords[1] = $this->passwords[1] == "" ? null : $this->passwords[1];
        $this->passwords[2] = $this->passwords[2] == "" ? null : $this->passwords[2];
        return $this;
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return mixed
     */
    protected function update(string $name, string|null $value)
    {
        Database::instance()->storeQuery("UPDATE configuration SET value = ? WHERE name = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'ss',
            $value,
            $name
        );
        $stmt->execute();
        return $this->getValue($name);
    }
}
