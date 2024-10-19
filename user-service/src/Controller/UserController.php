<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user')]
    public function index(): JsonResponse
    {
        $users = [
            ['name' => 'John Doe'],
            ['name' => 'Jane Doe'],
        ];
        return $this->json($users);
    }
}
