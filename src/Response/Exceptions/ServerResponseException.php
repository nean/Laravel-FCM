<?php

namespace LaravelFCM\Response\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ServerResponseException.
 */
class ServerResponseException extends Exception
{
    /**
     * retry after.
     *
     * @var int
     */
    public $retryAfter;

    /**
     * ServerResponseException constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $code = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();

        $this->retryAfter = (int) $response->getHeaderLine('Retry-After');

        parent::__construct($responseBody, $code);
    }
}
