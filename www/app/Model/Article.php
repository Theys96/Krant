<?php

namespace App\Model;

use App\Util\Singleton\Database;
use App\Util\Singleton\ErrorHandler;
use App\Util\Singleton\Session;
use DateTime;
use Exception;

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

    /** @var string */
    public string $context;

    /** @var int|null */
    public ?int $category_id;

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

    /** @var string */
    private const ACTIVE_CATEGORY_WHERE_QUERY = '(category IS NULL OR category IN (SELECT categories.id FROM categories LEFT JOIN editions ON categories.edition = editions.id WHERE editions.active = 1 AND categories.active = 1))';

    /**
     * @param int $id
     * @param string $status
     * @param string $title
     * @param string $contents
     * @param string $context
     * @param int|null $category_id
     * @param bool $ready
     * @param string $last_updated
     */
    public function __construct(int $id, string $status, string $title, string $contents, string $context, ?int $category_id, bool $ready, string $last_updated)
    {
        $this->id = $id;
        $this->status = $status;
        $this->title = $title;
        $this->contents = $contents;
        $this->context = $context;
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
        $timestamp = $change->timestamp->format('Y-m-d H:i:s');

        Database::instance()->storeQuery("UPDATE articles SET status = ?, title = ?, contents = ?, context = ?, category = ?, ready = ?, last_updated = ? WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'ssssiisi',
            $change->changed_status,
            $change->changed_title,
            $change->changed_contents,
            $change->changed_context,
            $change->changed_category_id,
            $change->changed_ready,
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
        Database::instance()->storeQuery("INSERT INTO `articles` (title, contents, context) VALUES ('', '', '')");
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
                $article_data['context'],
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
                $article_data['context'],
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
        return Article::getAllByQuery("SELECT * FROM articles WHERE " . self::ACTIVE_CATEGORY_WHERE_QUERY);
    }

    /**
     * @return Article[]
     */
    public static function getAllDrafts(): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE status='" . static::STATUS_DRAFT . "' AND " . self::ACTIVE_CATEGORY_WHERE_QUERY);
    }

    /**
     * @return Article[]
     */
    public static function getAllOpen(): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE status='" . static::STATUS_OPEN . "' AND " . self::ACTIVE_CATEGORY_WHERE_QUERY);
    }

    /**
     * @return Article[]
     */
    public static function getAllBinned(): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE status='" . static::STATUS_BIN . "' AND " . self::ACTIVE_CATEGORY_WHERE_QUERY);
    }

    /**
     * @return Article[]
     */
    public static function getAllPlaced(): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE status='" . static::STATUS_PLACED . "' AND " . self::ACTIVE_CATEGORY_WHERE_QUERY);
    }

    /**
     * @param Category $category
     * @return Article[]
     */
    public static function getAllByCategory(Category $category): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE category = " . ((int)$category->id));
    }

    /**
     * @param Edition $edition
     * @return Article[]
     */
    public static function getAllByEdition(Edition $edition): array
    {
        return Article::getAllByQuery("SELECT * FROM articles WHERE category IN (SELECT id FROM categories WHERE edition = " . ((int)$edition->id) . ")");
    }

    /**
     * @return string
     */
    public function getAuthorsString(): string
    {
        return htmlspecialchars(implode(', ', array_map(static function (User $author): string { return $author->username; }, $this->getAuthors())));
    }

    /**
     * @return User[]
     */
    private function getAuthors(): array
    {
        Database::instance()->storeQuery(
            <<<SQL
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
            $users[$user_data['id']] = new User($user_data['id'], $user_data['username'], $user_data['perm_level'], $user_data['active'], $user_data['alt_css']);
        }
        return $users;
    }

    /**
     * @return User[]
     */
    private function getCheckers(): array
    {
        $check_change = ArticleChange::CHANGE_TYPE_CHECK;
        Database::instance()->storeQuery(
            <<<SQL
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
            $users[$user_data['id']] = new User($user_data['id'], $user_data['username'], $user_data['perm_level'], $user_data['active'], $user_data['alt_css']);
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
            $this->title,
            $this->contents,
            $this->context,
            $this->category->id,
            $this->ready,
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
            $this->title,
            $this->contents,
            $this->context,
            $this->category->id,
            $this->ready,
            Session::instance()->getUser()->id
        );
        if ($article_change === null) {
            ErrorHandler::instance()->addError('Er is iets misgegaan bij het plaatsen van het stukje.');
            return $this;
        }
        return $this->applyChange($article_change);
    }

    /**
     * @return Article
     */
    public function moveToOpen(): Article
    {
        $article_change = ArticleChange::createNew(
            $this->id,
            ArticleChange::CHANGE_TYPE_TO_OPEN,
            static::STATUS_OPEN,
            $this->title,
            $this->contents,
            $this->context,
            $this->category->id,
            $this->ready,
            Session::instance()->getUser()->id
        );
        if ($article_change === null) {
            ErrorHandler::instance()->addError('Er is iets misgegaan bij het plaatsen van het stukje.');
            return $this;
        }
        return $this->applyChange($article_change);
    }

    /**
     * @param int $category_id
     * @return Article|$this
     */
    public function migrateToCategory(int $category_id): Article
    {
        $article_change = ArticleChange::createNew(
            $this->id,
            ArticleChange::CHANGE_TYPE_MIGRATION,
            $this->status,
            $this->title,
            $this->contents,
            $this->context,
            $category_id,
            $this->ready,
            Session::instance()->getUser()->id
        );
        if ($article_change === null) {
            ErrorHandler::instance()->addError('Er is iets misgegaan bij het overzetten van het stukje.');
            return $this;
        }
        return $this->applyChange($article_change);
    }
}
