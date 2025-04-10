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

    public int $id;

    public string $username;

    public int $perm_level;

    public bool $active;

    public int $alt_css;

    public int $highscore;

    public function __construct(int $id, string $username, int $perm_level, bool $active, int $alt_css, int $highscore)
    {
        $this->id = $id;
        $this->username = $username;
        $this->perm_level = $perm_level;
        $this->active = $active;
        $this->alt_css = $alt_css;
        $this->highscore = $highscore;
    }

    /**
     * Haalt een gebruiker uit de database aan de hand van de id.
     */
    public static function getById(int $id): ?User
    {
        Database::instance()->storeQuery('SELECT * FROM users WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        if ($user_data) {
            return new User($user_data['id'], $user_data['username'], $user_data['perm_level'], (bool) $user_data['active'], $user_data['alt_css'], $user_data['highscore']);
        }

        return null;
    }

    /**
     * Haalt alle gebruiker op aan de hand van de query.
     *
     * @return User[]
     */
    protected static function getAllByQuery(string $query): array
    {
        Database::instance()->storeQuery($query);
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($user_data = $result->fetch_assoc()) {
            $users[$user_data['id']] = new User($user_data['id'], $user_data['username'], $user_data['perm_level'], (bool) $user_data['active'], $user_data['alt_css'], $user_data['highscore']);
        }

        return $users;
    }

    /**
     * Haalt alle gebruikers op uit de database.
     *
     * @return User[]
     */
    public static function getAll(): array
    {
        return User::getAllByQuery('SELECT * FROM users ORDER BY username');
    }

    /**
     * Haalt alle actieve gebruikers op uit de database.
     *
     * @return User[]
     */
    public static function getAllActive(): array
    {
        return User::getAllByQuery('SELECT * FROM users WHERE active = 1 ORDER BY username');
    }

    /**
     * @return User[]
     */
    public static function getLiveDrafters(int $article_id): array
    {
        $update_type = ArticleChange::CHANGE_TYPE_DRAFT;

        return User::getAllByQuery(
            'SELECT * FROM users WHERE id IN (SELECT DISTINCT(user) FROM `article_updates` WHERE article_id = '.$article_id.' AND update_type = '.$update_type.' AND timestamp >= DATE_SUB(NOW(), INTERVAL 20 SECOND))'
        );
    }

    /**
     * Maakt een nieuwe gebruiker in de database.
     */
    public static function createNew(string $name, int $perm_level): ?User
    {
        Database::instance()->storeQuery('INSERT INTO `users` (username, perm_level) VALUES (?, ?)');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('si', $name, $perm_level);
        $stmt->execute();
        if ($stmt->insert_id) {
            return User::getById($stmt->insert_id);
        }

        return null;
    }

    /**
     * update deze gebruiker in de database.
     */
    public function update(string $name, int $perm_level, bool $active, int $alt_css): ?User
    {
        if (1 === $this->id) {
            if (false === $active) {
                ErrorHandler::instance()->addError(sprintf('Kan gebruiker \'%s\' niet deactiveren.', $this->username));

                return null;
            }
            if ($perm_level < 3) {
                ErrorHandler::instance()->addError(sprintf('Kan gebruiker \'%s\' geen lagere rol geven.', $this->username));

                return null;
            }
        }
        Database::instance()->storeQuery('UPDATE `users` SET username = ?, perm_level = ?, active = ?, alt_css = ? WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('siiii', $name, $perm_level, $active, $alt_css, $this->id);
        $stmt->execute();

        return User::getById($this->id);
    }

    /**
     * Update de highscore.
     */
    public function updateHighscore(int $highscore): ?User
    {
        Database::instance()->storeQuery('UPDATE `users` SET highscore = ? WHERE id = ? && highscore < ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('iii', $highscore, $this->id, $highscore);
        $stmt->execute();

        return User::getById($this->id);
    }

    /**
     * Geeft een array met de naam en score van de 5 gebruikers met de hoogst highscore, de aangegeven gebruiker uitgesloten.
     *
     * @param int $userid de user die moet worden buitengesloten
     *
     * @return array<array{0: string, 1: int}>
     */
    public static function getTopFive(int $userid): array
    {
        $users = User::getAllByQuery('SELECT * FROM users WHERE id != '.$userid.' AND highscore > 0 ORDER BY highscore DESC LIMIT 5');
        $top = [];
        foreach ($users as $user) {
            $top[] = [$user->username, $user->highscore];
        }

        return $top;
    }

    /**
     * Alle gegevens van user1 worden overgezet naar deze gebruiker. Daarna wordt user1 verwijderd.
     */
    public function combineUsers(User $user1): ?User
    {
        if (3 == $user1->perm_level) {
            ErrorHandler::instance()->addError(sprintf('Kan beheerder \'%s\' niet mergen.', $user1->username));

            return null;
        }
        // zet de reacties over als dat mogelijk is.
        Database::instance()->storeQuery('UPDATE IGNORE article_reactions SET user_id = ? WHERE user_id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $this->id, $user1->id);
        $stmt->execute();
        // verwijder de reacties die niet over gezet kunnen worden.
        Database::instance()->storeQuery('DELETE FROM article_reactions WHERE user_id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $user1->id);
        $stmt->execute();
        // zet de wijzegingen in de stukjes over.
        Database::instance()->storeQuery('UPDATE article_updates SET user = ? WHERE user = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $this->id, $user1->id);
        $stmt->execute();
        // zet de logs over.
        Database::instance()->storeQuery('UPDATE log SET user = ? WHERE user = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $this->id, $user1->id);
        $stmt->execute();
        // verwijder user1.
        Database::instance()->storeQuery('DELETE FROM users WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $user1->id);
        $stmt->execute();
        if (0 != $stmt->errno) {
            ErrorHandler::instance()->addError(sprintf('Kan \'%s\' niet mergen.', $user1->username));

            return null;
        }

        return User::getById($this->id);
    }

    /**
     * Geeft de permissie level van de gebruiker terug.
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
