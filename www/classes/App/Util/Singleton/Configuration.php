<?php

namespace App\Util\Singleton;

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
        Database::instance()->storeQuery("SELECT * FROM configuration");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        while ($result !== false && ($variables = $result->fetch_assoc()) !== null) {
            switch ($variables['name']) {
                case "schrijfregels":
                    $this->schrijfregels = $variables['value'];
                    break;
                case "min_checks":
                    $this->min_checks = (int)$variables['value'];
                    break;
                case "mail_address":
                    $this->mail_address = $variables['value'];
                    break;
                case "passwords":
                    $this->passwords = array_map(
                        static function (string $password): string|null {
                            return $password == "" ? null : $password;
                        },
                        explode(",", $variables['value'])
                    );
                    break;
            }
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
        $this-> schrijfregels = $this->schrijfregels == $schrijfregels ? $schrijfregels : $this->update("schrijfregels", $schrijfregels);
        $this->min_checks = $this->min_checks == $min_checks ? $min_checks : (int)$this->update("min_checks", $min_checks);
        $this->mail_address = $this->mail_address == $mail_address ? $mail_address : $this->update("mail_address", $mail_address);
        if ($this->passwords != $passwords) {
            $this->passwords = explode(",", $this->update("passwords", implode(",", array: $passwords)));
            $this->passwords[1] = $this->passwords[1] == "" ? null : $this->passwords[1];
            $this->passwords[2] = $this->passwords[2] == "" ? null : $this->passwords[2];
            $this->passwords[3] = $this->passwords[3] == "" ? null : $this->passwords[3];
        }
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
