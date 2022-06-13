<?php
namespace Model;

use Exception;
use Util\Singleton\Database;

class Category
{
    public int $id;
    public string $name;
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

    public static function getById(int $id): ?Category
    {
		$query = "SELECT * FROM categories WHERE id = ?";
        $stmt = Database::instance()->con->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $category_data = $stmt->get_result()->fetch_assoc();
        if ($category_data) {
            return new Category($category_data['id'], $category_data['name'], $category_data['description']);
        }
        return null;
    }

    public static function getAll(): array
    {
        $query = "SELECT * FROM categories";
        $stmt = Database::instance()->con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ( ($category_data = $result->fetch_assoc()) ) {
            $categories[$category_data['id']] = new Category($category_data['id'], $category_data['name'], $category_data['description']);
        }
        return $categories;
    }
}
