<?php

declare(strict_types=1);

namespace Pollen\Http;

use InvalidArgumentException;
use League\Uri\Contracts\UriInterface as LeagueUriInterface;
use League\Uri\Http;
use League\Uri\Components\Query;
use League\Uri\UriModifier;
use Psr\Http\Message\UriInterface;

class UriManipulation implements UrlManipulationInterface
{
    /**
     * Instance de l'url.
     * @var UriInterface
     */
    protected $uri;

    /**
     * @param UriInterface|LeagueUriInterface|string $uri
     *
     * @return void
     */
    public function __construct($uri)
    {
        $this->set($uri);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function appendSegment(string $segment): UrlManipulationInterface
    {
        return $this->set(UriModifier::appendSegment($this->uri, $segment));
    }

    /**
     * @inheritDoc
     */
    public function deleteSegment(string $segment): UrlManipulationInterface
    {
        if (preg_match("#{$segment}#", $this->uri->getPath(), $matches)) {
            return $this->set($this->uri->withPath(preg_replace("#{$matches[0]}#", '', $this->uri->getPath())));
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function decoded(bool $raw = true): string
    {
        return $raw ? rawurldecode((string)$this->uri) : urldecode((string)$this->uri);
    }

    /**
     * @inheritDoc
     */
    public function params(?string $key = null, ?string $default = null)
    {
        parse_str($this->uri->getQuery(), $params);

        return is_null($key) ? $params : ($params[$key] ?? $default);
    }

    /**
     * @inheritDoc
     */
    public function path(): ?string
    {
        return ($uri = $this->get()) ? $uri->getPath() : null;
    }

    /**
     * @inheritDoc
     */
    public function set($uri): UrlManipulationInterface
    {
        if (is_string($uri) && !($uri instanceof UriInterface) && !($uri instanceof LeagueUriInterface)) {
            throw new InvalidArgumentException(
                'Uri argument must be a string or UriInterface instance or LeagueUriInterface instance'
            );
        }

        $this->uri = is_string($uri) ? Http::createFromString($uri) : Http::createFromUri($uri);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function with(array $args): UrlManipulationInterface
    {
        $this->without(array_keys($args));

        return $this->set(UriModifier::appendQuery($this->uri, Query::createFromParams($args)));
    }

    /**
     * @inheritDoc
     */
    public function withFragment(string $fragment): UrlManipulationInterface
    {
        return $this->set($this->uri->withFragment($fragment));
    }

    /**
     * @inheritDoc
     */
    public function without(array $args): UrlManipulationInterface
    {
        return $this->set(UriModifier::removeParams($this->uri, ...$args));
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return (string)$this->uri;
    }
}