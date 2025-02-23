<?php

namespace App\Util\Singleton;

/**
 * Model voor de configuratie variabelen.
 */
class Configuration
{
    /** @var Configuration|null Singleton instance. */
    private static ?Configuration $instance = null;

    public string $schrijfregels;

    public int $min_checks;

    public ?string $mail_address;

    /** @var (string|null)[] */
    public array $passwords;

    /**
     * Returns the singleton instance.
     */
    public static function instance(): Configuration
    {
        if (null === self::$instance) {
            self::$instance = new Configuration();
        }

        return self::$instance;
    }

    public function __construct()
    {
        Database::instance()->storeQuery('SELECT * FROM configuration');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        while (false !== $result && ($variables = $result->fetch_assoc()) !== null) {
            switch ($variables['name']) {
                case 'schrijfregels':
                    $this->schrijfregels = $variables['value'];
                    break;
                case 'min_checks':
                    $this->min_checks = (int) $variables['value'];
                    break;
                case 'mail_address':
                    $this->mail_address = $variables['value'];
                    break;
                case 'passwords':
                    $this->passwords = array_map(
                        static function (string $password): ?string {
                            return '' == $password ? null : $password;
                        },
                        explode(',', $variables['value'])
                    );
                    break;
            }
        }
    }

    private function getValue($name): ?string
    {
        Database::instance()->storeQuery('SELECT * FROM configuration WHERE name = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            's',
            $name
        );
        $stmt->execute();
        $config_data = $stmt->get_result()->fetch_assoc();

        return $config_data ? $config_data['value'] : null;
    }

    public function updateAll(string $schrijfregels, int $min_checks, ?string $mail_address, array $passwords): Configuration
    {
        $this->schrijfregels = $this->schrijfregels == $schrijfregels ? $schrijfregels : $this->update('schrijfregels', $schrijfregels);
        $this->min_checks = $this->min_checks == $min_checks ? $min_checks : (int) $this->update('min_checks', (string) $min_checks);
        $this->mail_address = $this->mail_address == $mail_address ? $mail_address : $this->update('mail_address', $mail_address);
        if ($this->passwords != $passwords) {
            $this->passwords = explode(',', $this->update('passwords', implode(',', array: $passwords)));
            $this->passwords[1] = '' == $this->passwords[1] ? null : $this->passwords[1];
            $this->passwords[2] = '' == $this->passwords[2] ? null : $this->passwords[2];
            $this->passwords[3] = '' == $this->passwords[3] ? null : $this->passwords[3];
        }

        return $this;
    }

    protected function update(string $name, ?string $value)
    {
        Database::instance()->storeQuery('UPDATE configuration SET value = ? WHERE name = ?');
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
