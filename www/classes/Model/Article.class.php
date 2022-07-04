<?php

namespace Model;

use DateTime;
use Exception;
use Util\Singleton\Database;
use Util\Singleton\ErrorHandler;
use Util\Singleton\Session;

/**
 * Model voor stukjes.
 *
 * @property Category|null $category
 * @property User[] $authors
 * @property User[] $checkers
 */
class Article
{
    /** @var int */
    public int $id;

    /** @var string */
    public string $status;

    /** @var string */
    public string $title;

    /** @var string */
    public string $contents;

    /** @var int|null */
    protected ?int $category_id;

    /** @var User[]|null */
    protected ?array $authors = null;

    /** @var User[]|null */
    protected ?array $checkers = null;

    /** @var bool */
    public bool $ready;

    /** @var DateTime|null */
    public ?DateTime $last_updated;

    /** @var string */
    public const STATUS_DRAFT = 'draft';

    /** @var string */
    public const STATUS_OPEN = 'open';

    /** @var string */
    public const STATUS_PLACED = 'placed';

    /** @var string */
    public const STATUS_BIN = 'bin';

    /**
     * @param int $id
     * @param string $status
     * @param string $title
     * @param string $contents
     * @param int|null $category_id
     * @param bool $ready
     * @param string $last_updated
     */
    public function __construct(int $id, string $status, string $title, string $contents, ?int $category_id, bool $ready, string $last_updated)
    {
        $this->id = $id;
        $this->status = $status;
        $this->title = $title;
        $this->contents = $contents;
        $this->category_id = $category_id;
        $this->ready = $ready;
        try {
            $this->last_updated = new DateTime($last_updated);
        } catch (Exception) {
            $this->last_updated = null;
        }
    }

    /**
     * @param $value
     * @return Category|null
     */
    public function __get($value)
    {
        if ($value === 'category') {
            if ($this->category_id === null) {
                return null;
            }
            return Category::getById($this->category_id);
        } elseif ($value === 'authors') {
            if ($this->authors === null) {
                $this->authors = $this->getAuthors();
            }
        } elseif ($value === 'checkers') {
            if ($this->checkers === null) {
                $this->checkers = $this->getCheckers();
            }
        }
        return $this->$value;
    }

    /**
     * @param ArticleChange $change
     * @return Article
     */
    public function applyChange(ArticleChange $change): Article
    {
        $change = $change->updateFields(
            $change->changed_status === $this->status ? null : $change->changed_status,
            $change->changed_title === $this->title ? null : $change->changed_title,
            $change->changed_contents === $this->contents ? null : $change->changed_contents,
            $change->changed_category_id === $this->category_id ? null : $change->changed_category_id,
            $change->changed_ready === $this->ready ? null : $change->changed_ready,
        );

        $new_status = $change->changed_status ?? $this->status;
        $new_title = $change->changed_title ?? $this->title;
        $new_contents = $change->changed_contents ?? $this->contents;
        $new_category_id = $change->changed_category ? $change->changed_category->id : $this->category_id;
        $new_ready = $change->changed_ready ?? $this->ready;
        $timestamp = $change->timestamp->format('Y-m-d H:i:s');

        Database::instance()->storeQuery("UPDATE articles SET status = ?, title = ?, contents = ?, category = ?, ready = ?, last_updated = ? WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'sssiisi',
            $new_status,
            $new_title,
            $new_contents,
            $new_category_id,
            $new_ready,
            $timestamp,
            $this->id
        );
        $stmt->execute();
        return Article::getById($this->id);
    }

    /**
     * @return Article|null
     */
    public static function createNew(): ?Article
    {
        Database::instance()->storeQuery("INSERT INTO `articles` (title, contents) VALUES ('', '')");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        if ($stmt->insert_id) {
            return Article::getById($stmt->insert_id);
        }
        return null;
    }

    /**
     * @param int $id
     * @return Article|null
     */
    public static function getById(int $id): ?Article
    {
        Database::instance()->storeQuery("SELECT * FROM articles WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $article_data = $stmt->get_result()->fetch_assoc();
        if ($article_data) {
            return new Article(
                $article_data['id'],
                $article_data['status'],
                $article_data['title'],
                $article_data['contents'],
                $article_data['category'],
                $article_data['ready'],
                $article_data['last_updated']
            );
        }
        return null;
    }

    /**
     * @param string $query
     * @return Article[]
     */
    protected static function getAllByQuery(string $query): array
    {
        Database::instance()->storeQuery($query);
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        $articles = [];
        while (($article_data = $result->fetch_assoc())) {
            $articles[$article_data['id']] = new Article(
                $article_data['id'],
                $article_data['status'],
                $article_data['title'],
                $article_data['contents'],
                $article_data['category'],
                $article_data['ready'],
                $article_data['last_updated']
            );
        }
        return $articles;
    }

    /**
     * @return Article[]
     */
    public static function getAll(): array
    {
        return Article::getAllByQuery("SELECT * FROM articles");
    }

    /**
     * @return Article[]
     */
    public static function getAllOpen(): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE status='" . static::STATUS_OPEN . "'");
    }

    /**
     * @return Article[]
     */
    public static function getAllBinned(): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE status='" . static::STATUS_BIN . "'");
    }

    /**
     * @return Article[]
     */
    public static function getAllPlaced(): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE status='" . static::STATUS_PLACED . "'");
    }

    /**
     * @return User[]
     */
    private function getAuthors(): array
    {
        Database::instance()->storeQuery(<<<SQL
            SELECT * FROM users WHERE id IN (
                SELECT DISTINCT(user) AS author FROM `article_updates` 
                    LEFT JOIN `article_update_types` ON article_updates.update_type = article_update_types.id
                    WHERE `article_id` = ? AND author = 1
            )
        SQL
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while (($user_data = $result->fetch_assoc())) {
            $users[$user_data['id']] = new User($user_data['id'], $user_data['username'], $user_data['perm_level']);
        }
        return $users;
    }

    /**
     * @return User[]
     */
    private function getCheckers(): array
    {
        $check_change = ArticleChange::CHANGE_TYPE_CHECK;
        Database::instance()->storeQuery(<<<SQL
            SELECT * FROM users WHERE users.id IN (
                SELECT DISTINCT(au.user) AS author FROM `article_updates` au WHERE au.`article_id` = ? AND au.update_type = ?
                    AND au.id > (
                        SELECT MAX(au2.id) FROM `article_updates` au2
                            LEFT JOIN `article_update_types` ON au2.update_type = article_update_types.id
                            WHERE au2.`article_id` = au.article_id AND article_update_types.author = 1
                    )
            )
        SQL
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $this->id, $check_change);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while (($user_data = $result->fetch_assoc())) {
            $users[$user_data['id']] = new User($user_data['id'], $user_data['username'], $user_data['perm_level']);
        }
        return $users;
    }

    /**
     * @return Article
     */
    public function moveToBin(): Article
    {
        $article_change = ArticleChange::createNew(
            $this->id,
            ArticleChange::CHANGE_TYPE_TO_BIN,
            static::STATUS_BIN,
            null,
            null,
            null,
            null,
            Session::instance()->getUser()->id
        );
        if ($article_change === null) {
            ErrorHandler::instance()->addError('Er is iets misgegaan bij het verwijderen van het stukje.');
            return $this;
        }
        return $this->applyChange($article_change);
    }

    /**
     * @return Article
     */
    public function moveToPlaced(): Article
    {
        $article_change = ArticleChange::createNew(
            $this->id,
            ArticleChange::CHANGE_TYPE_TO_PLACED,
            static::STATUS_PLACED,
            null,
            null,
            null,
            null,
            Session::instance()->getUser()->id
        );
        if ($article_change === null) {
            ErrorHandler::instance()->addError('Er is iets misgegaan bij het plaatsen van het stukje.');
            return $this;
        }
        return $this->applyChange($article_change);
    }
}
