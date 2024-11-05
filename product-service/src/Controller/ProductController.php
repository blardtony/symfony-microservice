<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductController extends AbstractController
{
    public function __construct()
    {
    }
    #[Route('/products', name: 'products')]
    public function index(): JsonResponse
    {
        $products = [
            ['name' => 'Product 1'],
            ['name' => 'Product 2'],
        ];
        return new JsonResponse($products);
    }
    #[Route('/', name: 'products')]
    public function test(): Response
    {
        return new Response();
    }
}
