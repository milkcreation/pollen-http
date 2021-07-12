<?php

declare(strict_types=1);

namespace Pollen\Http;

use Pollen\Support\ParamsBagInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

/**
 * @mixin BaseRequest
 */
interface RequestInterface
{
    /**
     * Create a new instance from a Symfony request instance.
     *
     * @param BaseRequest $request
     *
     * @return static
     */
    public static function createFromBase(BaseRequest $request): RequestInterface;

    /**
     * Create a new instance from PSR-7 request instance.
     *
     * @param PsrRequest $psrRequest
     *
     * @return static
     */
    public static function createFromPsr(PsrRequest $psrRequest): RequestInterface;

    /**
     * Converts from a request instance to a Psr-7 HTTP request.
     *
     * @param BaseRequest|null $request
     *
     * @return PsrRequest|null
     */
    public static function createPsr(?BaseRequest $request = null): ?PsrRequest;

    /**
     * Retrieve the request based on request global variables.
     *
     * @return static
     */
    public static function getFromGlobals(): RequestInterface;

    /**
     * Get the absolute path to request root directory.
     *
     * @return string|null
     */
    public function getDocumentRoot(): ?string;

    /**
     * Get the request url prefix.
     *
     * @return static
     */
    public function getRewriteBase(): string;

    /**
     * Get the client user agent.
     *
     * @return string|null
     */
    public function getUserAgent(): ?string;

    /**
     * Returns instance of InputBag|Get a request variable value.
     *
     * @param string|int|null $key
     * @param mixed $default
     *
     * @return ParamsBagInterface|mixed
     */
    public function input($key = null, $default = null);

    /**
     * Check if request is JSON type.
     *
     * @return bool
     */
    public function isJson(): bool;

    /**
     * Returns instance of JsonBag|Get a request variable value from JSON request.
     *
     * @param string|int|null $key
     * @param mixed $default
     *
     * @return ParamsBagInterface|mixed
     */
    public function json($key = null, $default = null);

    /**
     * Get the request instance in PSR-7 format.
     *
     * @return ResponseInterface|null
     */
    public function psr(): ?PsrRequest;

    /**
     * Set the absolute path to request root directory.
     *
     * @param string
     *
     * @return static
     */
    public function setDocumentRoot(string $documentRoot): RequestInterface;
}