<?php declare(strict_types = 1);

namespace App\Repository\API;

use App\ApiClient\ApiClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SearchRepository
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function findBy(array $criteria, string $sort, string $perPage = '10'): ?ResponseInterface
    {
        $finalCriteria = \array_merge($criteria, ['sort' => $sort, 'per_page' => $perPage]);

        return $this->apiClient->get(
            $this->getPath(),
            $finalCriteria
        );
    }

    private function getPath(): string
    {
        return 'search';
    }
}
