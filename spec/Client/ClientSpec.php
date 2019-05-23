<?php

declare(strict_types=1);

namespace spec\Setono\DAO\Client;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Setono\DAO\Client\Client;
use Setono\DAO\Client\ClientInterface;

class ClientSpec extends ObjectBehavior
{
    private const CUSTOMER_ID = '123456';
    private const PASSWORD = 'p4ssw0rd';
    private const BASE_URL = 'https://api.dao.as';

    public function let(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ): void {
        $this->beConstructedWith($httpClient, $requestFactory, $streamFactory, self::CUSTOMER_ID, self::PASSWORD);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Client::class);
    }

    public function it_implements_client_interface(): void
    {
        $this->shouldImplement(ClientInterface::class);
    }

    public function it_gets(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        ResponseInterface $response,
        StreamInterface $stream
    ): void {
        $requestFactory
            ->createRequest('GET', self::BASE_URL.'/endpoint.php?kundeid='.self::CUSTOMER_ID.'&kode='.self::PASSWORD.'&param1=value1&param2=value2&format=json')
            ->shouldBeCalled();

        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($stream);
        $stream->__toString()->willReturn('{"items":[1,2,3]}');
        $httpClient->sendRequest(Argument::any())->willReturn($response);

        $this->get('endpoint.php', [
            'param1' => 'value1',
            'param2' => 'value2',
        ])->shouldReturn([
            'items' => [1, 2, 3],
        ]);
    }
}
