<?php

namespace Model;

use Util\Singleton\Database;
use Util\Singleton\Session;

/**
 * Categorieën model.
 */
class Category
{
    /** @var int */
    public int $id;

    /** @var string */
    public string $name;

    /** @var string */
    public string $description;

    /**
     * @param int $id
     * @param string $name
     * @param string $description
     */
    public function __construct(int $id, string $name, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @param string $name
     * @param string $description
     * @return Category|null
     */
    public static function createNew(string $name, string $description): ?Category
    {
        Database::instance()->storeQuery("INSERT INTO `categories` (name, description) VALUES (?, ?)");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ss', $name, $description);
        $stmt->execute();
        if ($stmt->insert_id) {
            return Category::getById($stmt->insert_id);
        }
        return null;
    }

    /**
     * @param string $name
     * @param string $description
     * @return Category|null
     */
    public function update(string $name, string $description): ?Category
    {
        Database::instance()->storeQuery("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ssi', $name, $description, $this->id);
        $stmt->execute();
        return Category::getById($this->id);
    }

    /**
     * @return bool
     */
    public function remove(): bool
    {
        foreach (Article::getAllByCategory($this) as $article) {
            $article_change = ArticleChange::createNew(
                $article->id,
                ArticleChange::CHANGE_TYPE_REMOVED_CATEGORY,
                null,
                null,
                null,
                null,
                null,
                Session::instance()->getUser()->id
            );
            $article->applyChange($article_change);
        }
        Database::instance()->storeQuery("DELETE FROM categories WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    /**
     * @param int $id
     * @return Category|null
     */
    public static function getById(int $id): ?Category
    {
        Database::instance()->storeQuery("SELECT * FROM categories WHERE id = ?");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $category_data = $stmt->get_result()->fetch_assoc();
        if ($category_data) {
            return new Category($category_data['id'], $category_data['name'], $category_data['description']);
        }
        return null;
    }

    /**
     * @return Category[]
     */
    public static function getAll(): array
    {
        Database::instance()->storeQuery("SELECT * FROM categories");
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while (($category_data = $result->fetch_assoc())) {
            $categories[$category_data['id']] = new Category($category_data['id'], $category_data['name'], $category_data['description']);
        }
        return $categories;
    }
}
