<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain;

final class Cart
{
    public function __construct(
        private readonly string $uuid,
        private readonly ?Customer $customer = null,
        private readonly ?string $paymentMethod = null,
        private array $items = [],
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(CartItem $item): void
    {
        $this->items[] = $item;
    }
}
