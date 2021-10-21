<?php declare(strict_types = 1);

namespace App\Repository\API;

use App\ApiClient\ApiClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PlatformRepository
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function findAll(): ?ResponseInterface
    {
        return $this->apiClient->get($this->getPath(), []);
    }

    private function getPath(): string
    {
        return 'platforms';
    }

}
