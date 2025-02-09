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

    /** @var string|null */
    protected string|null $schrijfregels;

    /** @var int|null */
    protected int|null $min_checks;

    /** @var string|null */
    protected string|null $mail_address;

    /** @var (string|null)[]|null */
    protected array|null $passwords;

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
        $this->schrijfregels = null;
        $this->min_checks = null;
        $this->mail_address = null;
        $this->passwords = null;
    }

    /**
     * @param $name
     * @return string|null|array|int
     * */
    public function __get($name)
    {
        if ($this->$name == null) {
            Database::instance()->storeQuery("SELECT * FROM configuration WHERE name = ?");
            $stmt = Database::instance()->prepareStoredQuery();
            $stmt->bind_param(
                's',
                $name
            );
            $stmt->execute();
            $config_data = $stmt->get_result()->fetch_assoc();
            $result = $config_data ? $config_data['value'] : null;
        } else {
            return $this->$name;
        }
        switch ($name) {
            case "min_checks":
                $this->min_checks = (int)$result;
                break;
            case "passwords":
                $this->passwords = explode(",", $result);
                $this->passwords[1] = $this->passwords[1] == "" ? null : $this->passwords[1];
                $this->passwords[2] = $this->passwords[2] == "" ? null : $this->passwords[2];
                $this->passwords[3] = $this->passwords[3] == "" ? null : $this->passwords[3];
                break;
            default:
                $this->$name = $result;
        }
        return $this->$name;
    }

    public function getComplete(): Configuration
    {
        $this->schrijfregels = $this->__get("schrijfregels");
        $this->min_checks = $this->__get("min_checks");
        $this->mail_address = $this->__get("mail_address");
        $this->passwords = $this->__get("passwords");
        return $this;
    }


    /**
     * @param $schrijfregels
     * @param $min_checks
     * @param $mail_address
     * @param $passwords
     * @return Configuration
     */
    public function updateAll(string $schrijfregels, int $min_checks, string|null $mail_address, array $passwords): Configuration
    {
        $this->schrijfregels = $this->update("schrijfregels", $schrijfregels);
        $this->min_checks = $this->update("min_checks", $min_checks);
        $this->mail_address = $this->update("mail_address", $mail_address);
        $this->passwords = $this->update("passwords", implode(",", array: $passwords));
        return $this;
    }

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
        return $this->__get($name);
    }
}
