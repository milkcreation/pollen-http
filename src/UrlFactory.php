<?php

declare(strict_types=1);

namespace Pollen\Http;

use League\Uri\Contracts\UriInterface as LeagueUri;
use League\Uri\Http;
use League\Uri\Components\Query;
use League\Uri\UriModifier;
use Psr\Http\Message\UriInterface;

class UrlFactory implements UrlFactoryInterface
{
    /**
     * Instance de l'url.
     * @var LeagueUri|UriInterface|null
     */
    protected $uri;

    /**
     * CONSTRUCTEUR
     *
     * @param UriInterface|LeagueUri|string $uri
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
    public function appendSegment(string $segment): UrlFactoryInterface
    {
        $this->uri = UriModifier::appendSegment($this->uri, $segment);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteSegment(string $segment): UrlFactoryInterface
    {
        if (preg_match("#{$segment}#", $this->uri->getPath(), $matches)) {
            $this->uri = $this->uri->withPath(preg_replace("#{$matches[0]}#", '', $this->uri->getPath()));
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get()
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
    public function set($uri): UrlFactoryInterface
    {
        if (!$uri instanceof UriInterface || !$uri instanceof LeagueUri) {
            $uri = Http::createFromString($uri);
        }

        $this->uri = $uri;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function with(array $args): UrlFactoryInterface
    {
        $this->without(array_keys($args));

        $this->uri = UriModifier::appendQuery($this->uri, Query::createFromParams($args));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withFragment(string $fragment): UrlFactoryInterface
    {
        $this->uri = $this->uri->withFragment($fragment);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function without(array $args): UrlFactoryInterface
    {
        $this->uri = UriModifier::removeParams($this->uri, ...$args);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return ($uri = $this->get()) ? (string)$uri : '';
    }
}