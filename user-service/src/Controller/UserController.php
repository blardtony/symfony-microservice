<?php

namespace App\Controller;

use App\Message\OrderCreated;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }
    #[Route('/users', name: 'app_user')]
    public function index(): JsonResponse
    {
        $users = [
            ['name' => 'John Doe'],
            ['name' => 'Jane Doe'],
        ];
        return new JsonResponse($users);
    }

    #[Route('/users/create-order', name: 'app_user_show')]
    public function createdOrder(): JsonResponse
    {
        $orderId = "123";
        $totalAmount = 99.99;

        // Envoi du message
        try {
            $this->messageBus->dispatch(new OrderCreated($orderId, $totalAmount));
        } catch (ExceptionInterface $e) {
            return new JsonResponse(['message' => 'Error while creating order'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse(['message' => 'Order created successfully', 'orderId' => $orderId], Response::HTTP_CREATED);
    }
}
