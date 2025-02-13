<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Service\CartService;
use Raketa\BackendTestTask\View\CartView;

readonly class GetCartController
{
    public function __construct(
        private CartService $cartService,
        private CartView $cartView
    ) {
    }

    public function get(RequestInterface $request): ResponseInterface
    {
        $cartId = session_id(); // или извлекаем из токена, query и т.д.

        // Получаем корзину через CartService
        $cart = $this->cartService->getCart($cartId);

        $response = new JsonResponse();

        // Корзина не найдена
        if (!$cart) {
            $response->getBody()->write(json_encode(
                ['message' => 'Cart not found'],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ));
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(404);
        }

        // Корзина найдена
        $response->getBody()->write(json_encode(
            $this->cartView->toArray($cart),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
