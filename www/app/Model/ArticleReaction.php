<?php

namespace App\Model;

use App\Util\Singleton\Database;

/**
 * Artikel reactie model.
 */
class ArticleReaction
{
    public int $id;

    public int $article_id;

    public int $user_id;

    public ?User $user;

    public string $reaction;

    public function __construct(int $id, int $article_id, int $user_id, string $reaction)
    {
        $this->id = $id;
        $this->article_id = $article_id;
        $this->user_id = $user_id;
        $this->reaction = $reaction;
        $this->user = User::getById($user_id);
    }

    public static function createNew(int $article_id, string $reaction, int $user_id): ?ArticleReaction
    {
        Database::instance()->storeQuery('INSERT INTO `article_reactions` (article_id, user_id, reaction) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE reaction = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('iiss', $article_id, $user_id, $reaction, $reaction);
        $stmt->execute();
        if ($stmt->insert_id) {
            return ArticleReaction::getById($stmt->insert_id);
        }

        return null;
    }

    public static function getById(int $id): ?ArticleReaction
    {
        Database::instance()->storeQuery('SELECT * FROM article_reactions WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $reaction_data = $stmt->get_result()->fetch_assoc();
        if ($reaction_data) {
            return new ArticleReaction($reaction_data['id'], $reaction_data['article_id'], $reaction_data['user_id'], $reaction_data['reaction']);
        }

        return null;
    }

    /**
     * @return ArticleReaction[]
     */
    public static function getByArticleId(int $id): array
    {
        Database::instance()->storeQuery('SELECT * FROM article_reactions WHERE article_id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reactions = [];
        while ($reaction_data = $result->fetch_assoc()) {
            $reactions[$reaction_data['id']] = new ArticleReaction($reaction_data['id'], $reaction_data['article_id'], $reaction_data['user_id'], $reaction_data['reaction']);
        }

        return $reactions;
    }

    public static function getByArticleIdAndUserId(int $article_id, int $user_id): ?ArticleReaction
    {
        Database::instance()->storeQuery('SELECT * FROM article_reactions WHERE article_id = ? AND user_id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ii', $article_id, $user_id);
        $stmt->execute();
        $reaction_data = $stmt->get_result()->fetch_assoc();
        if ($reaction_data) {
            return new ArticleReaction($reaction_data['id'], $reaction_data['article_id'], $reaction_data['user_id'], $reaction_data['reaction']);
        }

        return null;
    }

    /**
     * @return array<int, array{
     *     'reaction': string,
     *     'users': string[]
     * }>
     */
    public static function getByArticleIdGrouped(int $id): array
    {
        $reactions = ArticleReaction::getByArticleId($id);
        $grouped = [];
        foreach ($reactions as $reaction) {
            if (!array_key_exists($reaction->reaction, $grouped)) {
                $grouped[$reaction->reaction] = [];
            }
            $grouped[$reaction->reaction][] = $reaction->user->username ?? '?';
        }
        ksort($grouped);
        $response = [];
        foreach ($grouped as $reaction => $users) {
            $response[] = ['reaction' => $reaction, 'users' => $users];
        }

        return $response;
    }

    public function delete(): void
    {
        Database::instance()->storeQuery('DELETE FROM article_reactions WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
    }
}
