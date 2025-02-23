<?php

namespace App\Model;

use App\Util\Singleton\Database;
use App\Util\Singleton\Session;

/**
 * Model voor log entries.
 */
class Log
{
    public int $id;

    public string $type;

    public string $message;

    public ?int $user_id;

    public ?int $role;

    public ?User $user = null;

    public string $address;

    public string $request;

    public ?\DateTime $timestamp;

    /** @var string */
    public const TYPE_INFO = 'info';

    /** @var string */
    public const TYPE_WARNING = 'warning';

    /** @var string */
    public const TYPE_ERROR = 'error';

    /** @var string */
    public const TYPE_FEEDBACK = 'feedback';

    public function __construct(int $id, string $type, ?int $user_id, ?int $role, string $timestamp, string $address, string $request, string $message)
    {
        $this->id = $id;
        $this->type = $type;
        $this->user_id = $user_id;
        $this->role = $role;
        $this->address = $address;
        $this->request = $request;
        $this->message = $message;
        if (null !== $user_id) {
            $this->user = User::getById($user_id);
        }
        try {
            $this->timestamp = new \DateTime($timestamp);
        } catch (\Exception) {
            $this->timestamp = null;
        }
    }

    public static function createNew(string $type, ?int $user_id, ?int $role, string $message): ?Log
    {
        $address = $_SERVER['REMOTE_ADDR'];
        $request = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        Database::instance()->storeQuery('INSERT INTO `log` (type, user, role, address, request, message) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('siisss', $type, $user_id, $role, $address, $request, $message);
        $stmt->execute();
        if ($stmt->insert_id) {
            return Log::getById($stmt->insert_id);
        }

        return null;
    }

    public static function getById(int $id): ?Log
    {
        Database::instance()->storeQuery('SELECT * FROM log WHERE id = ?');
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
                $log_data['request'],
                $log_data['message']
            );
        }

        return null;
    }

    /**
     * @return Log[]
     */
    public static function getByType(string $type): array
    {
        Database::instance()->storeQuery('SELECT * FROM log WHERE type = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('s', $type);
        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        while ($log_data = $result->fetch_assoc()) {
            $logs[$log_data['id']] = new Log(
                $log_data['id'],
                $log_data['type'],
                $log_data['user'],
                $log_data['role'],
                $log_data['timestamp'],
                $log_data['address'],
                $log_data['request'],
                $log_data['message']
            );
        }

        return $logs;
    }

    public static function log(string $type, string $message): ?Log
    {
        return self::createNew(
            $type,
            Session::instance()->getUser()?->id,
            Session::instance()->getRole(),
            $message
        );
    }

    public static function logFeedback(string $message): ?Log
    {
        return self::log(self::TYPE_FEEDBACK, $message);
    }

    public static function logInfo(string $message): ?Log
    {
        return self::log(self::TYPE_INFO, $message);
    }

    public static function logWarning(string $message): ?Log
    {
        return self::log(self::TYPE_WARNING, $message);
    }

    public static function logError(string $message): ?Log
    {
        return self::log(self::TYPE_ERROR, $message);
    }
}
