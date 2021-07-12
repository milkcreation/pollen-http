<?php

declare(strict_types=1);

namespace Pollen\Http;

use Psr\Http\Message\UriInterface;
use League\Uri\Contracts\UriInterface as LeagueUri;

interface UrlManipulatorInterface
{
    /**
     * Resolve class as a string and returns url render.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Append an new segment to the URI path.
     *
     * @param string $segment
     *
     * @return static
     */
    public function appendSegment(string $segment): UrlManipulatorInterface;

    /**
     * Remove path segment from the URI path.
     *
     * @param string $segment
     *
     * @return static
     */
    public function deleteSegment(string $segment): UrlManipulatorInterface;

    /**
     * Get uri interface.
     *
     * @return UriInterface
     */
    public function get(): UriInterface;

    /**
     * Get decoded url.
     *
     * @param bool $raw
     *
     * @return string
     */
    public function decoded(bool $raw = true): string;

    /**
     * Get url parameters value, returns list of parameters if key is null.
     *
     * @param string|null $key
     * @param string|null $default
     *
     * @return array|string|null
     */
    public function params(?string $key = null, ?string $default = null);

    /**
     * Get url relative path.
     *
     * @return string|null
     */
    public function path(): ?string;

    /**
     * Set uri instance from Uri instance or url string.
     *
     * @param string|UriInterface|LeagueUri $uri
     *
     * @return static
     */
    public function set($uri): UrlManipulatorInterface;

    /**
     * Add parameters to the url.
     *
     * @param array $args
     *
     * @return static
     */
    public function with(array $args): UrlManipulatorInterface;

    /**
     * Add|Replace|remove url fragment.
     *
     * @param string $fragment
     *
     * @return static
     */
    public function withFragment(string $fragment): UrlManipulatorInterface;

    /**
     * Remove parameters from the url.
     *
     * @param string[] $args
     *
     * @return static
     */
    public function without(array $args): UrlManipulatorInterface;

    /**
     * Get url string render
     *
     * @return string
     */
    public function render(): string;
}