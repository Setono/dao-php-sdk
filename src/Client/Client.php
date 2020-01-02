<?php

declare(strict_types=1);

namespace Setono\DAO\Client;

use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\StringsException;
use function Safe\json_decode;
use Setono\DAO\Exception\ApiException;
use Setono\DAO\Exception\NotOkStatusCodeException;
use Webmozart\Assert\Assert;

final class Client implements ClientInterface
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var string */
    private $customerId;

    /** @var string */
    private $password;

    /** @var string */
    private $baseUrl;

    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        string $customerId,
        string $password,
        string $baseUrl = 'https://api.dao.as'
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->customerId = $customerId;
        $this->password = $password;
        $this->baseUrl = $baseUrl;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     * @throws InvalidArgumentException
     */
    public function get(string $endpoint, array $params = []): array
    {
        Assert::notContains($endpoint, '?', 'Do not add query parameters to the endpoint. Instead use the third argument, $params');

        $params = array_merge([
            'kundeid' => $this->customerId,
            'kode' => $this->password,
        ], $params);

        // overrides the format because this implementation only supports JSON
        $params['format'] = 'json';

        $url = $this->baseUrl . '/' . $endpoint . '?' . http_build_query($params, '', '&', \PHP_QUERY_RFC3986);

        $request = $this->requestFactory->createRequest('GET', $url);

        $response = $this->httpClient->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new NotOkStatusCodeException($request, $response, $response->getStatusCode());
        }

        $data = (array) json_decode((string) $response->getBody(), true);

        if (isset($data['status']) && 'FEJL' === $data['status']) {
            throw new ApiException($request, $response, $response->getStatusCode(), $data['fejlkode'], $data['fejltekst']);
        }

        return $data;
    }
}
