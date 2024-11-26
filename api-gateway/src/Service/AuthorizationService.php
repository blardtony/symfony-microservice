<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class AuthorizationService
{
    private array $authorizedClients = [
        'OAUTH_TEST_APP' => 'OAUTH_TEST_APP_SECRET',
    ];


    public function __construct(
        #[Autowire('%kernel.project_dir%/authorization_codes.json')]
        private readonly string $authorizationCodeListPath
    ) {
    }

    public function isAuthorized(string $clientId): bool
    {
        return isset($this->authorizedClients[$clientId]);
    }

    /**
     * @param array{ clientId: string, authorizationCode: string, expiredAt: string } $authorization
     * @return void
     */
    public function addAuthorizationCode(array $authorization): void
    {
        $codes = $this->readCodes();
        $codes[] = $authorization;
        $this->writeCodes($codes);
    }

    /**
     * @return array{ clientId: string, authorizationCode: string, expiredAt: string } | null
     */
    public function getAuthorization(string $authorizationCode): ?array
    {
        $codes = $this->readCodes();
        foreach ($codes as $code) {
            if ($code['authorizationCode'] === $authorizationCode) {
                return $code;
            }
        }
        return null;
    }
    private function readCodes(): array
    {
        return json_decode(file_get_contents($this->authorizationCodeListPath), true) ?? [];
    }

    private function writeCodes(array $codes): void
    {
        file_put_contents($this->authorizationCodeListPath, json_encode($codes));
    }
}