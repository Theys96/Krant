<?php

namespace App\Model;

use App\Util\Singleton\Database;
use App\Util\Singleton\ErrorHandler;

/**
 * User model.
 */
class User
{
    /** @var string */
    protected const ROLE_1 = 'Schrijver';

    /** @var string */
    protected const ROLE_2 = 'Nakijker';

    /** @var string */
    protected const ROLE_3 = 'Beheerder';

    /** @var int */
    public int $id;

    /** @var string */
    public string $username;

    /** @var int */
    public int $perm_level;

    /** @var bool */
    public bool $active;

    /** @var int */
    public int $alt_css;

    /**
     * @param int $id
     * @param string $username
     * @param int $perm_level
     * @param bool $active
     * @param int $alt_css
     */
    public function __construct(int $id, string $username, int $perm_level, bool $active, int $alt_css)
    {
        $this->id = $id;
        $this->username = $username;
        $this->perm_level = $perm_level;
        $this->active = $active;
        $this->alt_css = $alt_css;
    }

    /**
     * @param int $id
     * @return User|null
     */
    public static function getById(int $id): ?User
    {
        Database::instance()->storeQuery("SELECT * FROM users WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        if ($user_data) {
            return new User($user_data['id'], $user_data['username'], $user_data['perm_level'], $user_data['active'], $user_data['alt_css']);
        }
        return null;
    }

    /**
     * @return User[]
     */
    protected static function getAllByQuery(string $query): array
    {
        Database::instance()->storeQuery($query);
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while (($user_data = $result->fetch_assoc())) {
            $users[$user_data['id']] = new User($user_data['id'], $user_data['username'], $user_data['perm_level'], $user_data['active'], $user_data['alt_css']);
        }
        return $users;
    }

    /**
     * @return User[]
     */
    public static function getAll(): array
    {
        return User::getAllByQuery("SELECT * FROM users ORDER BY username");
    }

    /**
     * @return User[]
     */
    public static function getAllActive(): array
    {
        return User::getAllByQuery("SELECT * FROM users WHERE active = 1 ORDER BY username");
    }

    /**
     * @param int $article_id
     * @return User[]
     */
    public static function getLiveDrafters(int $article_id): array
    {
        $update_type = ArticleChange::CHANGE_TYPE_DRAFT;
        return User::getAllByQuery(
            "SELECT * FROM users WHERE id IN (SELECT DISTINCT(user) FROM `article_updates` WHERE article_id = " . $article_id . " AND update_type = " . $update_type . " AND timestamp >= DATE_SUB(NOW(), INTERVAL 20 SECOND))"
        );
    }

    /**
     * @param string $name
     * @param int $perm_level
     * @return User|null
     */
    public static function createNew(string $name, int $perm_level): ?User
    {
        Database::instance()->storeQuery("INSERT INTO `users` (username, perm_level) VALUES (?, ?)");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('si', $name, $perm_level);
        $stmt->execute();
        if ($stmt->insert_id) {
            return User::getById($stmt->insert_id);
        }
        return null;
    }

    /**
     * @param string $name
     * @param int $perm_level
     * @param bool $active
     * @param int $alt_css
     * @return User|null
     */
    public function update(string $name, int $perm_level, bool $active, int $alt_css): ?User
    {
        if ($this->id === 1) {
            if ($active === false) {
                ErrorHandler::instance()->addError(sprintf('Kan gebruiker \'%s\' niet deactiveren.', $this->username));
                return null;
            }
            if ($perm_level < 3) {
                ErrorHandler::instance()->addError(sprintf('Kan gebruiker \'%s\' geen lagere rol geven.', $this->username));
                return null;
            }
        }
        Database::instance()->storeQuery("UPDATE `users` SET username = ?, perm_level = ?, active = ?, alt_css = ? WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('siiii', $name, $perm_level, $active, $alt_css, $this->id);
        $stmt->execute();
        return User::getById($this->id);
    }

    /**
     * Alle gegevens van user1 worden overgezet naar deze gebruiker. Daarna wordt user1 verwijderd.
     * @param \Model\User $user1
     * @return User|null
     */
    public function combineUsers(User $user1): ?User
    {
        if ($user1->perm_level == 3) {
            ErrorHandler::instance()->addError(sprintf('Kan beheerder \'%s\' niet mergen.', $user1->username));
            return null;
        }
        //zet de reacties over als dat mogelijk is.
        Database::instance()->storeQuery("UPDATE IGNORE article_reactions SET user_id = ? WHERE user_id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $this->id, $user1->id);
        $stmt->execute();
        //verwijder de reacties die niet over gezet kunnen worden.
        Database::instance()->storeQuery("DELETE FROM article_reactions WHERE user_id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $user1->id);
        $stmt->execute();
        //zet de wijzegingen in de stukjes over.
        Database::instance()->storeQuery("UPDATE article_updates SET user = ? WHERE user = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $this->id, $user1->id);
        $stmt->execute();
        //zet de logs over.
        Database::instance()->storeQuery("UPDATE log SET user = ? WHERE user = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $this->id, $user1->id);
        $stmt->execute();
        //verwijder user1.
        Database::instance()->storeQuery("DELETE FROM users WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $user1->id);
        $stmt->execute();
        if ($stmt->errno != 0) {
            ErrorHandler::instance()->addError(sprintf('Kan \'%s\' niet mergen.', $user1->username));
            return null;
        }
        return User::getById($this->id);
    }

    /**
     * @return string
     */
    public function getPermLevelName(): string
    {
        switch ($this->perm_level) {
            default:
            case 1:
                return self::ROLE_1;

            case 2:
                return self::ROLE_2;

            case 3:
                return self::ROLE_3;
        }
    }
}
