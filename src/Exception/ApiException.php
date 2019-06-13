<?php

declare(strict_types=1);

namespace Setono\DAO\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiException extends RequestException
{
    /**
     * @var int|null
     */
    private $errorCode;

    /**
     * @var string|null
     */
    private $errorMessage;

    public function __construct(RequestInterface $request, ResponseInterface $response, int $statusCode, int $errorCode, string $errorMessage)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;

        parent::__construct($request, $response, $statusCode);
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
