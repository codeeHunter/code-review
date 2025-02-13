<?php

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Service\CartService;
use Raketa\BackendTestTask\View\CartView;

readonly class AddToCartController
{
    public function __construct(
        private CartService $cartService,
        private CartView $cartView
    ) {
    }

    public function post(RequestInterface $request): ResponseInterface
    {
        // Извлекаем данные из JSON
        $rawRequest = json_decode($request->getBody()->getContents(), true);

        $productUuid = $rawRequest['productUuid'] ?? null;
        $quantity = (int)($rawRequest['quantity'] ?? 1);

        // Используем session_id() в качестве идентификатора корзины (пример)
        $cartId = session_id();

        // Передаём в CartService
        $cart = $this->cartService->addItem($cartId, $productUuid, $quantity);

        // Формируем ответ
        $response = new JsonResponse();
        $response->getBody()->write(json_encode(
            [
                'status' => 'success',
                'cart' => $this->cartView->toArray($cart),
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
