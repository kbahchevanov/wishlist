<?php declare(strict_types = 1);

namespace App\ApiClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiClient
{
    public const METHOD_GET = 'GET';

    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $defaultHttpClient, string $apiKey)
    {
        $this->httpClient = $defaultHttpClient;
        $this->apiKey     = $apiKey;
    }

    public function get(string $path, ?array $parameters): ResponseInterface
    {
        $parameters = \array_merge($parameters, ['api_key' => $this->apiKey]);

        return $this->request(self::METHOD_GET, $path, $parameters);
    }

    private function request(
        string $method,
        string $path,
        ?array $parameters,
        array $customOptions = []
    ): ResponseInterface {
        return $this->httpClient->request(
            $method,
            \sprintf('%s%s', $path, $this->buildQuery($parameters)),
            $customOptions
        );
    }

    private function buildQuery(?array $parameters): string
    {
        if (empty($parameters)) {
            return '';
        }

        $parts = [];

        // Sort query parameters to ensure consistent api caching
        \ksort($parameters);

        foreach ($parameters as $key => $value) {
            $parts[] = \sprintf('%s=%s', $key, \rawurlencode($value));
        }

        return \sprintf('?%s', \implode('&', $parts));
    }
}
