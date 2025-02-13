<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Raketa\BackendTestTask\Repository\Entity\Product;
use RuntimeException;

class ProductRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function getByUuid(string $uuid): Product
    {
        // Используем параметризованный запрос
        $sql = "SELECT * FROM products WHERE uuid = :uuid LIMIT 1";
        $row = $this->connection->fetchAssociative($sql, ['uuid' => $uuid]);

        if (empty($row)) {
            throw new RuntimeException('Product not found');
        }

        return $this->make($row);
    }

    public function getByCategory(string $category): array
    {
        // Аналогично, параметризованный запрос
        $sql = "SELECT * FROM products WHERE is_active = 1 AND category = :category";
        $rows = $this->connection->fetchAllAssociative($sql, ['category' => $category]);

        return array_map(fn (array $row) => $this->make($row), $rows);
    }

    private function make(array $row): Product
    {
        return new Product(
            (int) $row['id'],
            (string) $row['uuid'],
            (bool) $row['is_active'],
            (string) $row['category'],
            (string) $row['name'],
            (string) ($row['description'] ?? ''),
            (string) ($row['thumbnail'] ?? ''),
            (float) $row['price']
        );
    }
}
