<?php
namespace Model;

use DateTime;
use Exception;
use Util\Singleton\Database;

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

    /** @var User[]|null  */
    protected ?array $authors = null;

    /** @var User[]|null  */
    protected ?array $checkers = null;

    /** @var bool */
    public bool $ready;

    /** @var DateTime|null */
    public ?DateTime $last_updated;

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

    public function applyChange(ArticleChange $change): Article
    {
        $new_status = $change->changed_status ?? $this->status;
        $new_title = $change->changed_title ?? $this->title;
        $new_contents = $change->changed_contents ?? $this->contents;
        $new_category_id = $change->changed_category ? $change->changed_category->id : $this->category_id;
        $timestamp = $change->timestamp->format('Y-m-d H:i:s');

		Database::instance()->storeQuery("UPDATE articles SET status = ?, title = ?, contents = ?, category = ?, last_updated = ? WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'sssisi',
            $new_status,
            $new_title,
            $new_contents,
            $new_category_id,
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
     * @return Article[]
     */
    public static function getAll(): array
    {
        Database::instance()->storeQuery("SELECT * FROM articles");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        $articles = [];
        while ( ($article_data = $result->fetch_assoc()) ) {
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
        SQL);
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ( ($user_data = $result->fetch_assoc()) ) {
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
        SQL);
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $this->id, $check_change);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ( ($user_data = $result->fetch_assoc()) ) {
            $users[$user_data['id']] = new User($user_data['id'], $user_data['username'], $user_data['perm_level']);
        }
        return $users;
    }
}
