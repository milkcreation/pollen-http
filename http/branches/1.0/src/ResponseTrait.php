<?php

declare(strict_types=1);

namespace Pollen\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

trait ResponseTrait
{
    /**
     * Create a new instance from a Symfony response instance.
     *
     * @param BaseResponse|ResponseInterface $response
     *
     * @return static
     */
    public static function createFromBase(BaseResponse $response): self
    {
        return new static($response->getContent(), $response->getStatusCode(), $response->headers->all());
    }

    /**
     * Create a new instance from PSR-7 response instance.
     *
     * @param PsrResponse $psrResponse
     * @param boolean $streamed
     *
     * @return static
     */
    public static function createFromPsr(PsrResponse $psrResponse, bool $streamed = false): self
    {
        return static::createFromBase((new HttpFoundationFactory())->createResponse($psrResponse, $streamed));
    }

    /**
     * Converts from a response instance to a Psr-7 HTTP response.
     *
     * @param BaseResponse|ResponseInterface|null $response
     *
     * @return PsrResponse
     */
    public static function createPsr($response = null): PsrResponse
    {
        if ($response === null) {
            $response = new static();
        }

        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        return $psrHttpFactory->createResponse($response);
    }

    /**
     * Get the response instance in PSR-7 format.
     *
     * @return PsrResponse
     */
    public function psr(): PsrResponse
    {
        return self::createPsr($this);
    }
}