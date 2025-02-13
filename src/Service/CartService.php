<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Service;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Ramsey\Uuid\Uuid;

class CartService
{
    public function __construct(
        private CartManager $cartManager,
        private ProductRepository $productRepository
    ) {
    }

    /**
     * Получить корзину из Redis по идентификатору (например, session_id()).
     * Если нет — вернуть null.
     */
    public function getCart(string $cartId): ?Cart
    {
        return $this->cartManager->getCartById($cartId);
    }

    /**
     * Создать новую корзину (если не существует) и вернуть её.
     * Можно передавать Customer и способ оплаты, если нужно.
     */
    public function createCart(
        string $cartId,
        ?Customer $customer = null,
        ?string $paymentMethod = null
    ): Cart {
        // Если нужно, можно проверить, есть ли уже корзина
        $existing = $this->cartManager->getCartById($cartId);
        if ($existing) {
            return $existing;
        }

        // Создаём новую корзину
        $cart = new Cart($cartId, $customer, $paymentMethod, []);

        // Сохраняем
        $this->cartManager->saveCart($cart);

        return $cart;
    }

    /**
     * Добавить товар в корзину. Если корзины нет — создаём новую.
     * Возвращаем обновлённую корзину.
     */
    public function addItem(string $cartId, string $productUuid, int $quantity): Cart
    {
        // Ищем корзину
        $cart = $this->cartManager->getCartById($cartId);

        // Если не найдена — создаём (для примера с пустым Customer)
        if (!$cart) {
            $cart = $this->createCart(
                $cartId,
                new Customer(
                    999,
                    'John',
                    'Doe',
                    'Tester',
                    'john@example.com'
                ),
                'card'
            );
        }

        // Ищем продукт
        $product = $this->productRepository->getByUuid($productUuid);

        // Добавляем товар
        $cart->addItem(new CartItem(
            Uuid::uuid4()->toString(),
            $product->getUuid(),
            $product->getPrice(),
            $quantity
        ));

        // Сохраняем
        $this->cartManager->saveCart($cart);

        return $cart;
    }
}