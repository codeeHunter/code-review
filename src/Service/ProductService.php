<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Service;

use Raketa\BackendTestTask\Repository\ProductRepository;
use Raketa\BackendTestTask\Repository\Entity\Product;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    /**
     * Получить список товаров по категории.
     */
    public function getProductsByCategory(string $category): array
    {
        return $this->productRepository->getByCategory($category);
    }

    /**
     * Получить товар по uuid.
     */
    public function getProductByUuid(string $uuid): Product
    {
        return $this->productRepository->getByUuid($uuid);
    }
}
