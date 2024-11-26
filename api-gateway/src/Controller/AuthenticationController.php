<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthorizationService;
use DateTimeInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class AuthenticationController extends AbstractController
{
    #[Route('/oauth2/authorize', name: 'oauth2_authorize')]
    public function index(Request $request, AuthorizationService $authorizationService): Response
    {
        $queryParameters = $request->query->all();

        $clientId = $queryParameters['client_id'] ?? null;

        if (!$clientId) {
            return new JsonResponse('Client ID is required', Response::HTTP_BAD_REQUEST);
        }

        if (!$authorizationService->isAuthorized($clientId)) {
            return new JsonResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $authorizationCode = Uuid::v4();
        $expiresIn = (new \DateTimeImmutable())->add(new \DateInterval('PT1H'));

        $authorizationService->addAuthorizationCode([
            'clientId' => $clientId,
            'authorizationCode' => $authorizationCode,
            'expiredAt' => $expiresIn->format(DateTimeInterface::ATOM)
        ]);

        return new JsonResponse([
            'authorization_code' => $authorizationCode,
            'expires_in' => $expiresIn->format(DateTimeInterface::ATOM),
        ]);
    }

    #[Route('/oauth2/token', name: 'oauth2_token', methods: ['POST'])]
    public function token(Request $request, AuthorizationService $authorizationService): Response
    {
        $queryParameters = $request->query->all();
        $login = json_decode($request->getContent(), true)['login'] ?? null;

        if (!$login) {
            return new JsonResponse('Login is required', Response::HTTP_BAD_REQUEST);
        }

        $authorizationCode = $queryParameters['authorization_code'] ?? null;

        $authorization = $authorizationService->getAuthorization($authorizationCode);

        if (!$authorization) {
            return new JsonResponse('Invalid authorization code', Response::HTTP_BAD_REQUEST);
        }

        if ($authorization['expiredAt'] < (new \DateTimeImmutable())->format(DateTimeInterface::ATOM)) {
            return new JsonResponse('Authorization code expired', Response::HTTP_BAD_REQUEST);
        }

        $payload = [
            'login' => $login,
            'exp' => (new \DateTimeImmutable())->add(new \DateInterval('PT1H'))->getTimestamp(),
        ];

       $accessToken = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

       return new JsonResponse([
           'access_token' => $accessToken,
           'token_type' => 'Bearer',
           'expires_in' => 3600,
       ]);
    }
}
