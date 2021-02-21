<?php

declare(strict_types=1);

namespace Pollen\Http;

use Pollen\Support\Env;
use Pollen\Support\Filesystem;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class Request extends BaseRequest implements RequestInterface
{
    /**
     * Instance basée sur les variable globales de la requête courante.
     * @var RequestInterface|null
     */
    protected static $globalsRequest;

    /**
     * Chemin absolue vers le répertoire racine de l'application.
     * @var string
     */
    protected $documentRoot;

    /**
     * @inheritDoc
     */
    public static function createFromBase(BaseRequest $request): RequestInterface
    {
        $newRequest = (new static())->duplicate(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all()
        );

        $newRequest->headers->replace($request->headers->all());

        $newRequest->content = $request->content;

        return $newRequest;
    }

    /**
     * @inheritDoc
     */
    public static function createFromPsr(PsrRequest $psrRequest): RequestInterface
    {
        return self::createFromBase((new HttpFoundationFactory())->createRequest($psrRequest));
    }

    /**
     * @inheritDoc
     */
    public static function createPsr(?BaseRequest $request = null): ?PsrRequest
    {
        if ($request === null) {
            $request = self::getFromGlobals();
        }

        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        return $psrHttpFactory->createRequest($request);
    }

    /**
     * @inheritDoc
     */
    public static function getFromGlobals(): RequestInterface
    {
        if (self::$globalsRequest === null) {
            self::$globalsRequest = static::createFromBase(BaseRequest::createFromGlobals());
        }
        return self::$globalsRequest;
    }

    /**
     * @inheritDoc
     */
    public function getDocumentRoot(): ?string
    {
        if ($this->documentRoot === null) {
            if ($file = $this->server->get('SCRIPT_FILENAME')) {
                $this->documentRoot = dirname($file);
            } elseif (!$this->documentRoot = $this->server->get('CONTEXT_DOCUMENT_ROOT')) {
                $this->documentRoot = getcwd() ?: null;
            }

            if ($this->documentRoot !== null) {
                $this->documentRoot = Filesystem::normalizePath($this->documentRoot);
            }
        }

        return $this->documentRoot;
    }

    /**
     * @inheritDoc
     */
    public function getRewriteBase(): string
    {
        if ($appUrl = Env::get('APP_URL')) {
            if (preg_match('/^' . preg_quote($this->getSchemeAndHttpHost(), '/') . '(.*)/', $appUrl, $matches)) {
                return isset($matches[1]) ? '/' . rtrim(ltrim($matches[1], '/'), '/') : '';
            }
            return '';
        }
        return $this->server->get('CONTEXT_PREFIX', '');
    }

    /**
     * @inheritDoc
     */
    public function psr(): ?PsrRequest
    {
        return static::createPsr($this);
    }

    /**
     * @inheritDoc
     */
    public function setDocumentRoot(string $documentRoot): RequestInterface
    {
        $this->documentRoot = $documentRoot;

        return $this;
    }
}