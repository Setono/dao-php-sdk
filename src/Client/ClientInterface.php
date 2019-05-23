<?php

declare(strict_types=1);

namespace Setono\DAO\Client;

interface ClientInterface
{
    /**
     * Sends a GET request to the specified endpoint with the given query params.
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     */
    public function get(string $endpoint, array $params = []): array;
}
