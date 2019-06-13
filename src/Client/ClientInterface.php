<?php

declare(strict_types=1);

namespace Setono\DAO\Client;

use Setono\DAO\Exception\ApiException;
use Setono\DAO\Exception\NotOkStatusCodeException;

interface ClientInterface
{
    /**
     * Sends a GET request to the specified endpoint with the given query params.
     *
     * @throws NotOkStatusCodeException
     * @throws ApiException
     */
    public function get(string $endpoint, array $params = []): array;
}
