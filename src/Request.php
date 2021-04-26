<?php

declare(strict_types=1);

namespace Pollen\Http;


use Pollen\Support\Env;
use Pollen\Support\Filesystem;
use Pollen\Support\ParamsBag;
use Pollen\Support\ParamsBagInterface;
use Pollen\Support\Str;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Throwable;

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
     * Liste des variables décodées issues du contenu d'une requête de type JSON.
     * @var ParamsBagInterface|null
     */
    protected $jsonBag;

    /**
     * Liste des variables de requête.
     * @var ParamsBagInterface|null
     */
    protected $inputBag;

    /**
     * @inheritDoc
     */
    public static function createFromBase(BaseRequest $request): RequestInterface
    {
        $files = $request->files->all();

        $newRequest = (new static())->duplicate(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            is_array($files) ? array_filter($files) : $files,
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
                return !empty($matches[1]) ? '/' . rtrim(ltrim($matches[1], '/'), '/') : '';
            }
            return '';
        }
        return $this->server->get('CONTEXT_PREFIX', '');
    }

    /**
     * @inheritDoc
     */
    public function getUserAgent(): ?string
    {
        return $this->headers->get('User-Agent');
    }

    /**
     * @inheritDoc
     */
    public function input($key = null, $default = null)
    {
        if ($this->inputBag === null) {
            if ($this->isJson()) {
                $data = $this->json() instanceof ParamsBag ? $this->json()->all() : [];
            } elseif (!in_array($this->getRealMethod(), ['GET', 'HEAD'])) {
                $data = $this->request->all();
            } else {
                $data = $this->query->all();
            }

            $this->inputBag = new ParamsBag(array_merge($data, $this->query->all()));
        }

        if ($key === null) {
            return $this->inputBag;
        }

        return $this->inputBag->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function isJson(): bool
    {
        return Str::contains($this->headers->get('CONTENT_TYPE') ?? '', ['/json', '+json']);
    }

    /**
     * @inheritDoc
     */
    public function json($key = null, $default = null)
    {
        if ($this->jsonBag === null) {
            try {
                $data = json_decode($this->getContent(), true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                $data = [];
            }

            $this->jsonBag = new ParamsBag($data);
        }

        if ($key === null) {
            return $this->jsonBag;
        }

        return $this->jsonBag->get($key, $default);
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