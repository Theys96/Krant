<?php
namespace Model;

use Util\Singleton\Database;

/**
 * User model.
 */
class User
{
    /** @var int */
    public int $id;

    /** @var string */
    public string $username;

    /** @var int */
    public int $perm_level;

    /**
     * @param int $id
     * @param string $username
     * @param int $perm_level
     */
    public function __construct(int $id, string $username, int $perm_level)
    {
        $this->id = $id;
        $this->username = $username;
        $this->perm_level = $perm_level;
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
            return new User($user_data['id'], $user_data['username'], $user_data['perm_level']);
        }
        return null;
    }

    /**
     * @return User[]
     */
    public static function getAll(): array
    {
        Database::instance()->storeQuery("SELECT * FROM users");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ( ($user_data = $result->fetch_assoc()) ) {
            $users[$user_data['id']] = new User($user_data['id'], $user_data['username'], $user_data['perm_level']);
        }
        return $users;
    }
}
