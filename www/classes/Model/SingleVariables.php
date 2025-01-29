<?php

namespace Model;
use Util\Singleton\Database;

/**
 * Model voor de single variables.
 */
class SingleVariables
{
    /** @var SingleVariables|null Singleton instance. */
    private static ?SingleVariables $instance = null;

    /** @var string */
    public string $schrijfregels;

    /** @var int */
    public int $min_checks;

    /** @var string */
    public string $mail_address;

    /**
     * Returns the singleton instance.
     * @return SingleVariables
     */
    public static function instance(): SingleVariables
    {
        if (self::$instance === null) {
            self::$instance = new SingleVariables();
        }
        return self::$instance;
    }

    public function __construct()
    {
        Database::instance()->storeQuery("SELECT * FROM single_variables WHERE id = 1");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $variables = $stmt->get_result()->fetch_assoc();
        if($variables) {
            $this->schrijfregels = $variables["schrijfregels"];
            $this->min_checks = $variables["min_checks"];
            $this->mail_address= $variables["mail_address"];
        }
    }


    /**
     * @param ArticleChange $change
     * @return Article
     */
    public function update(string $schrijfregels, int $min_checks, string $mail_address): SingleVariables
    {
        $this->schrijfregels = $schrijfregels;
        $this->min_checks = $min_checks;
        $this->mail_address = $mail_address;
        Database::instance()->storeQuery("UPDATE single_variables SET schrijfregels = ?, min_checks = ?, mail_address = ? WHERE id = 1");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'sis',
            $schrijfregels,
            $min_checks,
            $mail_address,
        );
        $stmt->execute();

        return $this;
    }
}