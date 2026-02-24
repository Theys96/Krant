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

    public int $article_number;

    public int $picture_number;

    public int $wjd_number;

    public function __construct(int $id, string $name, string $description, int $article_number, int $picture_number, int $wjd_number)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->article_number = $article_number;
        $this->picture_number = $picture_number;
        $this->wjd_number = $wjd_number;
    }

    public static function createNew(string $name, string $description, int $article_number, int $picture_number, int $wjd_number): ?Category
    {
        $edition = Edition::getActive()?->id;
        if (null === $edition) {
            return null;
        }
        Database::instance()->storeQuery('INSERT INTO `categories` (name, description, article_number, picture_number, wjd_number, edition) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ssiiii', $name, $description, $article_number, $picture_number, $wjd_number, $edition);
        $stmt->execute();
        if ($stmt->insert_id) {
            return Category::getById($stmt->insert_id);
        }

        return null;
    }

    public function update(string $name, string $description, int $article_number, int $picture_number, int $wjd_number): ?Category
    {
        Database::instance()->storeQuery('UPDATE categories SET name = ?, description = ?, article_number = ?, picture_number = ?, wjd_number = ? WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ssiiii', $name, $description, $article_number, $picture_number, $wjd_number, $this->id);
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
            return new Category($category_data['id'], $category_data['name'], $category_data['description'], $category_data['article_number'], $category_data['picture_number'], $category_data['wjd_number']);
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
            $categories[$category_data['id']] = new Category($category_data['id'], $category_data['name'], $category_data['description'], $category_data['article_number'], $category_data['picture_number'], $category_data['wjd_number']);
        }

        return $categories;
    }
}
