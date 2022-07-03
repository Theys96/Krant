<?php
namespace Model;

use DateTime;
use Exception;
use Util\Singleton\Database;

/**
 * Stukjes-updates.
 *
 * @property Article|null $article
 * @property Category|null $changed_category
 */
class ArticleChange
{
    /** @var int  */
    public const CHANGE_TYPE_DRAFT = 1;
    /** @var int  */
    public const CHANGE_TYPE_NEW_ARTICLE = 2;
    /** @var int  */
    public const CHANGE_TYPE_EDIT = 3;
    /** @var int  */
    public const CHANGE_TYPE_CHECK = 4;
    /** @var int  */
    public const CHANGE_TYPE_TO_BIN = 5;

    /** @var int */
    public int $id;

    /** @var int */
    protected int $article_id;

    /** @var string */
    public string $update_type;

    /** @var string|null */
    public ?string $changed_status;

    /** @var string|null */
    public ?string $changed_title;

    /** @var string|null */
    public ?string $changed_contents;

    /** @var int|null */
    public ?int $changed_category_id;

    /** @var bool|null */
    public ?bool $changed_ready;

    /** @var int */
    protected int $user_id;

    /** @var DateTime|null */
    public ?DateTime $timestamp;

    /**
     * @param int $id
     * @param int $article_id
     * @param string $update_type
     * @param string|null $changed_status
     * @param string|null $changed_title
     * @param string|null $changed_contents
     * @param int|null $changed_category_id
     * @param bool|null $changed_ready
     * @param int $user_id
     * @param string $timestamp
     */
    public function __construct(
        int $id,
        int $article_id,
        string $update_type,
        ?string $changed_status,
        ?string $changed_title,
        ?string $changed_contents,
        ?int $changed_category_id,
        ?bool $changed_ready,
        int $user_id,
        string $timestamp
    ) {
        $this->id = $id;
        $this->article_id = $article_id;
        $this->update_type = $update_type;
        $this->changed_status = $changed_status;
        $this->changed_title = $changed_title;
        $this->changed_contents = $changed_contents;
        $this->changed_category_id = $changed_category_id;
        $this->changed_ready = $changed_ready;
        $this->user_id = $user_id;
        try {
            $this->timestamp = new DateTime($timestamp);
        } catch (Exception) {
            $this->timestamp = null;
        }
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function __get($value)
    {
        if ($value === 'article') {
            return Article::getById($this->article_id);
        } elseif ($value === 'changed_category') {
            return $this->changed_category_id === null ? null : Category::getById($this->changed_category_id);
        }
        return $this->$value;
    }

    /**
     * @param int $article_id
     * @param int $update_type
     * @param string|null $changed_status
     * @param string|null $changed_title
     * @param string|null $changed_contents
     * @param int|null $changed_category_id
     * @param bool|null $changed_ready
     * @param int $user_id
     * @return ArticleChange|null
     */
    public static function createNew(
        int $article_id,
        int $update_type,
        ?string $changed_status,
        ?string $changed_title,
        ?string $changed_contents,
        ?int $changed_category_id,
        ?bool $changed_ready,
        int $user_id
    ): ?ArticleChange
    {
        Database::instance()->storeQuery(
            "INSERT INTO `article_updates` (article_id, update_type, changed_status, changed_title, changed_contents, changed_category, changed_ready, user) VALUES (?,?,?,?,?,?,?,?)"
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'iisssiii',
            $article_id,
            $update_type,
            $changed_status,
            $changed_title,
            $changed_contents,
            $changed_category_id,
            $changed_ready,
            $user_id
        );
        $stmt->execute();
        if ($stmt->insert_id) {
            return ArticleChange::getById($stmt->insert_id);
        }
        return null;
    }

    /**
     * @param string|null $changed_title
     * @param string|null $changed_contents
     * @param int|null $changed_category_id
     * @param bool|null $changed_ready
     * @return ArticleChange
     */
    public function updateFields(
        ?string $changed_status,
        ?string $changed_title,
        ?string $changed_contents,
        ?int $changed_category_id,
        ?bool $changed_ready
    ): ArticleChange
    {
        Database::instance()->storeQuery(
            "UPDATE `article_updates` SET changed_status = ?, changed_title = ?, changed_contents = ?, changed_category = ?, changed_ready = ? WHERE id = ?"
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'sssiii',
            $changed_status,
            $changed_title,
            $changed_contents,
            $changed_category_id,
            $changed_ready,
            $this->id
        );
        $stmt->execute();
        return ArticleChange::getById($this->id);
    }

    /**
     * Update a draft ArticleChange to 'open'.
     *
     * @param int $change_type
     * @return ArticleChange
     */
    public function openDraft(int $change_type): ArticleChange
    {
        Database::instance()->storeQuery(
            "UPDATE `article_updates` SET update_type = ?, changed_status = '" . Article::STATUS_OPEN . "' WHERE id = ?"
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $change_type, $this->id);
        $stmt->execute();
        return ArticleChange::getById($this->id);
    }

    /**
     * @param int $id
     * @return ArticleChange|null
     */
    public static function getById(int $id): ?ArticleChange
    {
		Database::instance()->storeQuery(
            "SELECT article_updates.*, article_update_types.description FROM article_updates LEFT JOIN article_update_types ON article_updates.update_type = article_update_types.id WHERE article_updates.id = ?"
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $change_data = $stmt->get_result()->fetch_assoc();
        if ($change_data) {
            return new ArticleChange(
                $change_data['id'],
                $change_data['article_id'],
                $change_data['description'],
                $change_data['changed_status'],
                $change_data['changed_title'],
                $change_data['changed_contents'],
                $change_data['changed_category'],
                $change_data['changed_ready'],
                $change_data['user'],
                $change_data['timestamp']
            );
        }
        return null;
    }
}
