<?php

namespace App\Model;

use App\Util\Singleton\Database;

/**
 * Categorieën model.
 */
class Edition
{
    public int $id;

    public string $name;

    public string $description;

    public bool $active;

    public function __construct(int $id, string $name, string $description, bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->active = $active;
    }

    public static function createNew(string $name, string $description): ?Edition
    {
        Database::instance()->storeQuery('INSERT INTO `editions` (name, description, active) VALUES (?, ?, 0)');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ss', $name, $description);
        $stmt->execute();
        if ($stmt->insert_id) {
            return Edition::getById($stmt->insert_id);
        }

        return null;
    }

    public function setActive(): bool
    {
        Database::instance()->storeQuery('UPDATE editions SET active = 0');
        Database::instance()->prepareStoredQuery()->execute();
        Database::instance()->storeQuery('UPDATE editions SET active = 1 WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $this->active = true;

        return $stmt->affected_rows > 0;
    }

    public function update(string $name, string $description): ?Edition
    {
        Database::instance()->storeQuery('UPDATE editions SET name = ?, description = ? WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('ssi', $name, $description, $this->id);
        $stmt->execute();

        return Edition::getById($this->id);
    }

    public function countArticles(?string $status = null): int
    {
        $query = 'SELECT COUNT(*) AS count FROM articles WHERE category IN (SELECT id FROM categories WHERE edition = ?)'
            .(null !== $status ? ' AND status = ?' : '');
        Database::instance()->storeQuery($query);
        $stmt = Database::instance()->prepareStoredQuery();
        if (null === $status) {
            $stmt->bind_param('i', $this->id);
        } else {
            $stmt->bind_param('is', $this->id, $status);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return (int) $result['count'];
    }

    public static function getById(int $id): ?Edition
    {
        Database::instance()->storeQuery('SELECT * FROM editions WHERE id = ?');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $edition_data = $stmt->get_result()->fetch_assoc();
        if ($edition_data) {
            return new Edition($edition_data['id'], $edition_data['name'], $edition_data['description'], $edition_data['active']);
        }

        return null;
    }

    /**
     * @return Edition[]
     */
    public static function getAll(): array
    {
        Database::instance()->storeQuery('SELECT * FROM editions');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        $editions = [];
        while ($edition_data = $result->fetch_assoc()) {
            $editions[$edition_data['id']] = new Edition($edition_data['id'], $edition_data['name'], $edition_data['description'], $edition_data['active']);
        }

        return $editions;
    }

    public static function getActive(): ?Edition
    {
        Database::instance()->storeQuery('SELECT * FROM editions WHERE active = 1');
        $stmt = Database::instance()->prepareStoredQuery();
        $stmt->execute();
        $result = $stmt->get_result();

        if ($edition_data = $result->fetch_assoc()) {
            return new Edition($edition_data['id'], $edition_data['name'], $edition_data['description'], $edition_data['active']);
        }

        return null;
    }
}
