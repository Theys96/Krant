<?php

namespace App\Model;

use App\Util\Singleton\Database;

/**
 * Stukjes-updates.
 *
 * @property Article|null  $article
 * @property Category|null $changed_category
 * @property User|null     $user
 */
class ArticleChange
{
    /** @var int */
    public const CHANGE_TYPE_DRAFT = 1;
    /** @var int */
    public const CHANGE_TYPE_NEW_ARTICLE = 2;
    /** @var int */
    public const CHANGE_TYPE_EDIT = 3;
    /** @var int */
    public const CHANGE_TYPE_CHECK = 4;
    /** @var int */
    public const CHANGE_TYPE_TO_BIN = 5;
    /** @var int */
    public const CHANGE_TYPE_TO_PLACED = 6;
    /** @var int */
    public const CHANGE_TYPE_REMOVED_CATEGORY = 7;
    /** @var int */
    public const CHANGE_TYPE_TO_OPEN = 8;
    /** @var int */
    public const CHANGE_TYPE_MIGRATION = 9;

    public int $id;

    protected int $article_id;

    public int $update_type_id;

    public string $update_type_description;

    public string $changed_status;

    public string $changed_title;

    public string $changed_contents;

    public string $changed_context;

    public ?int $changed_category_id;

    public ?bool $changed_ready;

    protected int $user_id;

    public ?\DateTime $timestamp;

    private ?User $user = null;

    public function __construct(
        int $id,
        int $article_id,
        int $update_type_id,
        string $update_type_description,
        string $changed_status,
        string $changed_title,
        string $changed_contents,
        string $changed_context,
        ?int $changed_category_id,
        bool $changed_ready,
        int $user_id,
        string $timestamp,
    ) {
        $this->id = $id;
        $this->article_id = $article_id;
        $this->update_type_id = $update_type_id;
        $this->update_type_description = $update_type_description;
        $this->changed_status = $changed_status;
        $this->changed_title = $changed_title;
        $this->changed_contents = $changed_contents;
        $this->changed_context = $changed_context;
        $this->changed_category_id = $changed_category_id;
        $this->changed_ready = $changed_ready;
        $this->user_id = $user_id;
        try {
            $this->timestamp = new \DateTime($timestamp);
        } catch (\Exception) {
            $this->timestamp = null;
        }
    }

    /**
     * @return mixed|null
     */
    public function __get($value)
    {
        if ('article' === $value) {
            return Article::getById($this->article_id);
        } elseif ('changed_category' === $value) {
            return null === $this->changed_category_id ? null : Category::getById($this->changed_category_id);
        } elseif ('user' === $value) {
            if (null === $this->user) {
                $this->user = User::getById($this->user_id);
            }

            return $this->user;
        }

        return $this->$value;
    }

    public static function createNew(
        int $article_id,
        int $update_type,
        ?string $changed_status,
        ?string $changed_title,
        ?string $changed_contents,
        ?string $changed_context,
        ?int $changed_category_id,
        ?bool $changed_ready,
        int $user_id,
    ): ?ArticleChange {
        Database::instance()->storeQuery(
            'INSERT INTO `article_updates` (article_id, update_type, changed_status, changed_title, changed_contents, changed_context, changed_category, changed_ready, user) VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'iissssiii',
            $article_id,
            $update_type,
            $changed_status,
            $changed_title,
            $changed_contents,
            $changed_context,
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

    public function updateFields(
        string $changed_status,
        string $changed_title,
        string $changed_contents,
        string $changed_context,
        ?int $changed_category_id,
        bool $changed_ready,
    ): ArticleChange {
        Database::instance()->storeQuery(
            'UPDATE `article_updates` SET changed_status = ?, changed_title = ?, changed_contents = ?, changed_context = ?, changed_category = ?, changed_ready = ?, timestamp = CURRENT_TIMESTAMP WHERE id = ?'
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param(
            'ssssiii',
            $changed_status,
            $changed_title,
            $changed_contents,
            $changed_context,
            $changed_category_id,
            $changed_ready,
            $this->id
        );
        $stmt->execute();

        return ArticleChange::getById($this->id);
    }

    /**
     * Update a draft ArticleChange to 'open'.
     */
    public function openDraft(int $change_type): ArticleChange
    {
        Database::instance()->storeQuery(
            "UPDATE `article_updates` SET update_type = ?, changed_status = '".Article::STATUS_OPEN."' WHERE id = ?"
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $change_type, $this->id);
        $stmt->execute();

        return ArticleChange::getById($this->id);
    }

    /**
     * @return ArticleChange[]
     */
    public static function getByArticleId(int $id): array
    {
        Database::instance()->storeQuery(
            'SELECT article_updates.*, article_update_types.id AS update_type_id, article_update_types.description AS update_type_description FROM article_updates LEFT JOIN article_update_types ON article_updates.update_type = article_update_types.id WHERE article_updates.article_id = ? ORDER BY article_updates.timestamp DESC'
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $article_changes = [];
        while (($change_data = $result->fetch_assoc()) != false) {
            $article_changes[$change_data['id']] = new ArticleChange(
                $change_data['id'],
                $change_data['article_id'],
                $change_data['update_type_id'],
                $change_data['update_type_description'],
                $change_data['changed_status'],
                $change_data['changed_title'],
                $change_data['changed_contents'],
                $change_data['changed_context'],
                $change_data['changed_category'],
                $change_data['changed_ready'],
                $change_data['user'],
                $change_data['timestamp']
            );
        }

        return $article_changes;
    }

    public static function getById(int $id): ?ArticleChange
    {
        Database::instance()->storeQuery(
            'SELECT article_updates.*, article_update_types.id AS update_type_id, article_update_types.description AS update_type_description FROM article_updates LEFT JOIN article_update_types ON article_updates.update_type = article_update_types.id WHERE article_updates.id = ?'
        );
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $change_data = $stmt->get_result()->fetch_assoc();
        if ($change_data) {
            return new ArticleChange(
                $change_data['id'],
                $change_data['article_id'],
                $change_data['update_type_id'],
                $change_data['update_type_description'],
                $change_data['changed_status'],
                $change_data['changed_title'],
                $change_data['changed_contents'],
                $change_data['changed_context'],
                $change_data['changed_category'],
                $change_data['changed_ready'],
                $change_data['user'],
                $change_data['timestamp']
            );
        }

        return null;
    }
}
