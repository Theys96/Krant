<?php

namespace Model;

use DateTime;
use Exception;
use Util\Singleton\Database;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;

/**
 * Model voor feedback.
 *
 */
class Feedback
{
    /** @var int */
    public int $id;

    /** @var string */
    public string $text;

    /** @var int */
    protected int $user_id;

    /** @var User|null */
    protected ?User $user = null;

    /** @var DateTime|null */
    public ?DateTime $timestamp;

    /**
     * @param int $id
     * @param string $text
     * @param int $user_id
     * @param string $timestamp
     */
    public function __construct(int $id, string $text, int $user_id, string $timestamp)
    {
        $this->id = $id;
        $this->text = $text;
        $this->user_id = $user_id;
        $this->user = User::getById($user_id);
        try {
            $this->timestamp = new DateTime($timestamp);
        } catch (Exception) {
            $this->timestamp = null;
        }
    }

    /**
     * @param string $text
     * @param int $user_id
     * @return Feedback|null
     */
    public static function createNew(string $text, int $user_id): ?Feedback
    {
        Database::instance()->storeQuery("INSERT INTO `feedback` (text, user) VALUES (?, ?)");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('si', $text, $user_id);
        $stmt->execute();
        if ($stmt->insert_id) {
            return Feedback::getById($stmt->insert_id);
        }
        return null;
    }

    /**
     * @param int $id
     * @return Feedback|null
     */
    public static function getById(int $id): ?Feedback
    {
        Database::instance()->storeQuery("SELECT * FROM feedback WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $feedback_data = $stmt->get_result()->fetch_assoc();
        if ($feedback_data) {
            return new Feedback(
                $feedback_data['id'],
                $feedback_data['text'],
                $feedback_data['user'],
                $feedback_data['timestamp']
            );
        }
        return null;
    }
}
