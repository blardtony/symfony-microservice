<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\OrderCreated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class OrderCreatedHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }
    public function __invoke(OrderCreated $orderCreated): void
    {
        $orderId = $orderCreated->getOrderId();
        $totalAmount = $orderCreated->getTotalAmount();
        $this->logger->info("Order created with id: $orderId and total amount: $totalAmount");
    }
}