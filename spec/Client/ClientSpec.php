<?php

declare(strict_types=1);

namespace spec\Setono\DAO\Client;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\StringsException;
use Setono\DAO\Client\Client;
use Setono\DAO\Client\ClientInterface;
use Setono\DAO\Exception\ApiException;
use Setono\DAO\Exception\NotOkStatusCodeException;

class ClientSpec extends ObjectBehavior
{
    private const CUSTOMER_ID = '123456';

    private const PASSWORD = 'p4ssw0rd';

    private const BASE_URL = 'https://api.dao.as';

    public function let(HttpClientInterface $httpClient, RequestFactoryInterface $requestFactory): void
    {
        $this->beConstructedWith($httpClient, $requestFactory, self::CUSTOMER_ID, self::PASSWORD);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Client::class);
    }

    public function it_implements_client_interface(): void
    {
        $this->shouldImplement(ClientInterface::class);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws StringsException
     */
    public function it_gets(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        ResponseInterface $response,
        StreamInterface $stream
    ): void {
        $requestFactory
            ->createRequest('GET', self::BASE_URL . '/endpoint.php?kundeid=' . self::CUSTOMER_ID . '&kode=' . self::PASSWORD . '&param1=value1&param2=value2&format=json')
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

    /**
     * @throws ClientExceptionInterface
     */
    public function it_throws_not_ok_status_code_exception(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        ResponseInterface $response
    ): void {
        $requestFactory
            ->createRequest('GET', Argument::any())
            ->shouldBeCalled();

        $response->getStatusCode()->willReturn(500);
        $httpClient->sendRequest(Argument::any())->willReturn($response);

        $this->shouldThrow(NotOkStatusCodeException::class)->during('get', ['endpoint']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws StringsException
     */
    public function it_throws_api_exception(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        RequestInterface $request,
        ResponseInterface $response,
        StreamInterface $stream
    ): void {
        $requestFactory
            ->createRequest('GET', Argument::any())
            ->willReturn($request)
        ;

        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($stream);
        $stream->__toString()->willReturn('{"status":"FEJL", "fejlkode": 101, "fejltekst": "Wrong login"}');
        $httpClient->sendRequest($request)->willReturn($response);

        $exception = new ApiException($request->getWrappedObject(), $response->getWrappedObject(), 200, 101, 'Wrong login');

        $this->shouldThrow($exception)->during('get', ['endpoint']);
    }
}
