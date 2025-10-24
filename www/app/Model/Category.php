<?php

namespace App\Model;

use App\Util\Singleton\Database;
use App\Util\Singleton\Session;

/**
 * Categorieën model.
 */
class Category
{
    public int $id;

    public string $name;

    public string $description;

    public int $article_amount;

    public int $picture_amount;

    public int $wjd_amount;

    public function __construct(int $id, string $name, string $description, int $article_amount, int $picture_amount, int $wjd_amount)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->article_amount = $article_amount;
        $this->picture_amount = $picture_amount;
        $this->wjd_amount = $wjd_amount;
    }

    public static function createNew(string $name, string $description, int $article_amount, int $picture_amount, int $wjd_amount): ?Category
    {
        $edition = Edition::getActive()?->id;
        if (null === $edition) {
            return null;
        }
        Database::instance()->storeQuery('INSERT INTO `categories` (name, description, article_amount, picture_amount, wjd_amount, edition) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ssiiii', $name, $description, $article_amount, $picture_amount, $wjd_amount, $edition);
        $stmt->execute();
        if ($stmt->insert_id) {
            return Category::getById($stmt->insert_id);
        }

        return null;
    }

    public function update(string $name, string $description, int $article_amount, int $picture_amount, int $wjd_amount): ?Category
    {
        Database::instance()->storeQuery('UPDATE categories SET name = ?, description = ?, article_amount = ?, picture_amount = ?, wjd_amount = ? WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ssiiii', $name, $description, $article_amount, $picture_amount, $wjd_amount, $this->id);
        $stmt->execute();

        return Category::getById($this->id);
    }

    public function remove(): bool
    {
        foreach (Article::getAllByCategory($this) as $article) {
            $article_change = ArticleChange::createNew(
                $article->id,
                ArticleChange::CHANGE_TYPE_REMOVED_CATEGORY,
                $article->status,
                $article->title,
                $article->contents,
                $article->context,
                null,
                $article->ready,
                $article->picture,
                $article->wjd,
                Session::instance()->getUser()->id
            );
            $article->applyChange($article_change);
        }
        Database::instance()->storeQuery('UPDATE categories SET active = 0 WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $this->id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    public static function getById(int $id): ?Category
    {
        Database::instance()->storeQuery('SELECT * FROM categories WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $category_data = $stmt->get_result()->fetch_assoc();
        if ($category_data) {
            return new Category($category_data['id'], $category_data['name'], $category_data['description'], $category_data['article_amount'], $category_data['picture_amount'], $category_data['wjd_amount']);
        }

        return null;
    }

    /**
     * @return Category[]
     */
    public static function getAll(?Edition $edition = null): array
    {
        $query = (null === $edition)
            ? 'SELECT * FROM categories WHERE active = 1 AND edition IN (SELECT id FROM editions WHERE active = 1)'
            : 'SELECT * FROM categories WHERE active = 1 AND edition = '.((int) $edition->id);
        Database::instance()->storeQuery($query);
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ($category_data = $result->fetch_assoc()) {
            $categories[$category_data['id']] = new Category($category_data['id'], $category_data['name'], $category_data['description'], $category_data['article_amount'], $category_data['picture_amount'], $category_data['wjd_amount']);
        }

        return $categories;
    }
}
