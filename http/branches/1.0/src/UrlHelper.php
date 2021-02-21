<?php

declare(strict_types=1);

namespace Pollen\Http;

use BadMethodCallException;
use Symfony\Component\HttpFoundation\UrlHelper as BaseUrlHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

/**
 * @mixin BaseUrlHelper
 */
class UrlHelper
{
    /**
     * @var BaseUrlHelper
     */
    protected $delegate;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param RequestInterface|null $request
     */
    public function __construct(?RequestInterface $request = null)
    {
        $this->request = $request ?? Request::getFromGlobals();
        $requestStack =  new RequestStack();
        $requestStack->push($this->request);
        $this->delegate = new BaseUrlHelper($requestStack);
    }

    /**
     * Délégation d'appel des méthodes de l'UrlHelper de Symfony.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->delegate->{$method}(...$arguments);
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    'Delegate [%s] method call [%s] throws an exception: %s',
                    BaseUrlHelper::class,
                    $method,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Récupération de l'url absolue vers un chemin.
     *
     * @param string $path
     *
     * @return string
     */
    public function getAbsoluteUrl(string $path = ''): string
    {
        $path = $this->request->getRewriteBase() . sprintf('/%s', ltrim($path, '/'));

        return $this->delegate->getAbsoluteUrl($path);
    }

    /**
     * Récupération de l'url relative vers un chemin.
     *
     * @param string $path
     *
     * @return string
     */
    public function getRelativePath(string $path): string
    {
        $path = $this->request->getRewriteBase() . sprintf('/%s', ltrim($path, '/'));

        return $this->delegate->getAbsoluteUrl($path);
    }
}