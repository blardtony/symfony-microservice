<?php
declare(strict_types=1);

namespace App\Message;

final readonly class OrderCreated
{

    public function __construct(
        private string $orderId,
        private float  $totalAmount,
    ) {
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }
}