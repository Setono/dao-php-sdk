<?php

declare(strict_types=1);

namespace Setono\DAO\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\StringsException;
use function Safe\json_decode;
use Setono\DAO\Exception\RequestFailedException;

final class Client implements ClientInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var string
     */
    private $customerId;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $customerId,
        string $password,
        string $baseUrl = 'https://api.dao.as'
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->customerId = $customerId;
        $this->password = $password;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->sendRequest('GET', $endpoint, $params);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    private function sendRequest(string $method, string $endpoint, array $params = []): array
    {
        $params = array_merge([
            'kundeid' => $this->customerId,
            'kode' => $this->password,
        ], $params);

        // overrides the format because this implementation only supports JSON
        $params['format'] = 'json';

        $url = $this->baseUrl.'/'.$endpoint.'?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        $request = $this->requestFactory->createRequest($method, $url);

        $response = $this->httpClient->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new RequestFailedException($request, $response, $response->getStatusCode());
        }

        $data = (array) json_decode((string) $response->getBody(), true);

        if (isset($data['status']) && 'FEJL' === $data['status']) {
            throw new RequestFailedException($request, $response, $response->getStatusCode());
        }

        return $data;
    }
}
