<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }
    #[Route('/api', name: 'app_api')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }
    #[Route('/api/{service}', name: 'api_proxy')]
    public function proxyRequest(string $service, Request $request): JsonResponse
    {
        $serviceUrl = $this->getServiceUrl($service);
        if (!$serviceUrl) {
            return new JsonResponse('Service not found', Response::HTTP_NOT_FOUND);
        }

        $method = $request->getMethod();
        $options = [
            'headers' => $request->headers->all(),
            'query' => $request->query->all(),
        ];

        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options['json'] = json_decode($request->getContent(), true);
        }
        $urlWhithoutApi = str_replace('/api', '', $request->getRequestUri());
        $response = $this->httpClient->request(
            $method,
            $serviceUrl . $urlWhithoutApi,
            $options
        );
        return new JsonResponse(
            $response->toArray(false),
            $response->getStatusCode(),
            []
        );
    }
    private function getServiceUrl(string $service): ?string
    {
        $serviceMap = [
            'users' => 'USER_SERVICE_URL',
        ];

        if (!isset($serviceMap[$service])) {
            return null;
        }

        return $_ENV[$serviceMap[$service]];
    }
}
