<?php

namespace Model;

use DateTime;
use Exception;
use Util\Singleton\Database;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;

/**
 * Model voor log entries.
 *
 */
class Log
{
    /** @var int */
    public int $id;

    /** @var string */
    public string $type;

    /** @var string */
    public string $message;

    /** @var int */
    public int $user_id;

    /** @var int */
    public int $role;

    /** @var User|null */
    public ?User $user = null;

    /** @var string */
    public string $address;

    /** @var DateTime|null */
    public ?DateTime $timestamp;

    /** @var string */
    public const TYPE_INFO = 'info';

    /** @var string */
    public const TYPE_WARNING = 'warning';

    /** @var string */
    public const TYPE_ERROR = 'error';

    /** @var string */
    public const TYPE_FEEDBACK = 'feedback';

    /**
     * @param int $id
     * @param string $type
     * @param int $user_id
     * @param int $role
     * @param string $timestamp
     * @param string $address
     * @param string $message
     */
    public function __construct(int $id, string $type, int $user_id, int $role, string $timestamp, string $address, string $message)
    {
        $this->id = $id;
        $this->type = $type;
        $this->user_id = $user_id;
        $this->role = $role;
        $this->address = $address;
        $this->message = $message;
        $this->user = User::getById($user_id);
        try {
            $this->timestamp = new DateTime($timestamp);
        } catch (Exception) {
            $this->timestamp = null;
        }
    }

    /**
     * @param string $type
     * @param int $user_id
     * @param int $role
     * @param string $message
     * @return Log|null
     */
    public static function createNew(string $type, int $user_id, int $role, string $message): ?Log
    {
        $address = $_SERVER['REMOTE_ADDR'];
        Database::instance()->storeQuery("INSERT INTO `log` (type, user, role, address, message) VALUES (?, ?, ?, ?, ?)");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('siiss', $type, $user_id, $role, $address, $message);
        $stmt->execute();
        if ($stmt->insert_id) {
            return Log::getById($stmt->insert_id);
        }
        return null;
    }

    /**
     * @param int $id
     * @return Log|null
     */
    public static function getById(int $id): ?Log
    {
        Database::instance()->storeQuery("SELECT * FROM log WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $log_data = $stmt->get_result()->fetch_assoc();
        if ($log_data) {
            return new Log(
                $log_data['id'],
                $log_data['type'],
                $log_data['user'],
                $log_data['role'],
                $log_data['timestamp'],
                $log_data['address'],
                $log_data['message'],
            );
        }
        return null;
    }
}
