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
     * @inheritDoc
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->delegate->{$method}(...$arguments);
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    'Delegate UrlHelper method call [%s] throws an exception: %s',
                    $method,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getAbsoluteUrl(string $path = ''): string
    {
        $path = $this->request->getRewriteBase() . sprintf('/%s', ltrim($path, '/'));

        return $this->delegate->getAbsoluteUrl($path);
    }
}