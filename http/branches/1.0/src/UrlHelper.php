<?php

declare(strict_types=1);

namespace Pollen\Http;

use BadMethodCallException;
use Exception;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\UrlHelper as BaseUrlHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Pollen\Support\Proxy\HttpRequestProxy;
use Throwable;

/**
 * @mixin BaseUrlHelper
 */
class UrlHelper
{
    use HttpRequestProxy;

    /**
     * Delegate UrlHelper.
     * @var BaseUrlHelper
     */
    protected BaseUrlHelper $delegate;

    /**
     * @param RequestInterface|null $request
     */
    public function __construct(?RequestInterface $request = null)
    {
        if ($request !== null) {
            $this->setHttpRequest($request);
        }

        $requestStack =  new RequestStack();
        $requestStack->push($this->httpRequest());

        $this->delegate = new BaseUrlHelper($requestStack);
    }

    /**
     * Delegate method call from Symfony UrlHelper.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->delegate->{$method}(...$arguments);
        } catch (Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    'Delegate [%s] method call [%s] throws an exception: %s',
                    BaseUrlHelper::class,
                    $method,
                    $e->getMessage()
                ), 0, $e
            );
        }
    }

    /**
     * Get uri instance from a path.
     *
     * @param string $path Relative path or absolute path belongs to the request document.
     *
     * @return UriInterface
     */
    public function getAbsoluteUri(string $path = ''): UriInterface
    {
        return (new UrlManipulator($this->getAbsoluteUrl($path)))->get();
    }

    /**
     * Get absolute url from a path.
     *
     * @param string $path Relative path or absolute path belongs to the request document.
     *
     * @return string
     */
    public function getAbsoluteUrl(string $path = ''): string
    {
        $docRoot = $this->httpRequest()->getDocumentRoot();

        if (preg_match('/^' . preg_quote($docRoot, '/') . '(.*)/', $path, $matches)) {
            $path = $matches[1];
        }

        $path = $this->httpRequest()->getRewriteBase() . sprintf('/%s', ltrim($path, '/'));

        return $this->delegate->getAbsoluteUrl($path);
    }

    /**
     * Get relative url from a path.
     *
     * @param string $path Relative path or absolute path belongs to the request document.
     *
     * @return string
     */
    public function getRelativePath(string $path): string
    {
        $docRoot = $this->httpRequest()->getDocumentRoot();

        if (preg_match('/^' . preg_quote($docRoot, '/') . '(.*)/', $path, $matches)) {
            $path = $matches[1];
        }

        $path = $this->httpRequest()->getRewriteBase() . sprintf('/%s', ltrim($path, '/'));

        return sprintf('/%s', ltrim($this->delegate->getRelativePath($path), '/'));
    }

    /**
     * Get url scope.
     *
     * @return string
     */
    public function getScope(): string
    {
        return ($path = $this->httpRequest()->getRewriteBase()) ? sprintf('/%s/', rtrim(ltrim($path, '/'), '/')) : '/';
    }
}