<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\Customer;
use Raketa\BackendTestTask\Infrastructure\ConnectorFacade;
use Exception;

class CartManager extends ConnectorFacade
{
    private LoggerInterface $logger;

    public function __construct(
        string $host,
        int $port,
        ?string $password,
        LoggerInterface $logger
    ) {
        parent::__construct($host, $port, $password, 1);
        $this->build();
        $this->logger = $logger;
    }

    public function createNewCart(
        string $uuid,
        ?Customer $customer = null,
        ?string $paymentMethod = null
    ): Cart {
        return new Cart($uuid, $customer, $paymentMethod, []);
    }

    /**
     * Сохранить корзину в Redis
     */
    public function saveCart(Cart $cart): void
    {
        try {
            $this->getConnector()?->set($cart->getUuid(), $cart);
        } catch (Exception $e) {
            $this->logger->error('Error saving cart', ['exception' => $e]);
        }
    }

    /**
     * Получить корзину из Redis
     */
    public function getCart(): ?Cart
    {
        try {
            return $this->getConnector()?->get(session_id());
        } catch (Exception $e) {
            $this->logger->error('Error retrieving cart', ['exception' => $e]);
            return null;
        }
    }

    public function getCartById(string $cartId): ?Cart
    {
        try {
            return $this->getConnector()?->get($cartId);
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving cart by id', ['exception' => $e]);
            return null;
        }
    }
}
