<?php

declare(strict_types=1);

namespace Setono\DAO\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class NotOkStatusCodeExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_values(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $exception = new NotOkStatusCodeException($request, $response, 200);

        $this->assertSame($request, $exception->getRequest());
        $this->assertSame($response, $exception->getResponse());
        $this->assertSame(200, $exception->getStatusCode());
    }
}
