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

    /**
     * @throws Exception
     */
    public static function getCategoryById(int $id): ?Category
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
}
