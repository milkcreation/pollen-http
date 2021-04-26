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
     * Creation d'une instance depuis une instance de requête symfony.
     *
     * @param BaseRequest $request
     *
     * @return static
     */
    public static function createFromBase(BaseRequest $request): RequestInterface;

    /**
     * Création d'une instance depuis une requête PSR-7.
     *
     * @param PsrRequest $psrRequest
     *
     * @return static
     */
    public static function createFromPsr(PsrRequest $psrRequest): RequestInterface;

    /**
     * Convertion d'une instance de requête en requête HTTP Psr-7
     *
     * @param BaseRequest|null $request
     *
     * @return PsrRequest|null
     */
    public static function createPsr(?BaseRequest $request = null): ?PsrRequest;

    /**
     * Récupération de l'instance basée sur les variables globales.
     *
     * @return static
     */
    public static function getFromGlobals(): RequestInterface;

    /**
     * Récupération du chemin absolu vers le répertoire racine de l'application.
     *
     * @return string
     */
    public function getDocumentRoot(): ?string;

    /**
     * Récupération du préfixe d'url.
     *
     * @return static
     */
    public function getRewriteBase(): string;

    /**
     * Récupération des informations de navigateur.
     *
     * @return string|null
     */
    public function getUserAgent(): ?string;

    /**
     * Récupération de variables passées en arguments ou dans le contenu de la requête (ex. JSON|$_REQUEST).
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return ParamsBagInterface|mixed
     */
    public function input($key = null, $default = null);

    /**
     * Vérifie si la requête retourne un contenu de type JSON.
     *
     * @return bool
     */
    public function isJson(): bool;

    /**
     * Récupération de variables JSON passées à la requête.
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return ParamsBagInterface|mixed
     */
    public function json($key = null, $default = null);

    /**
     * Conversion au format PSR-7.
     *
     * @return ResponseInterface|null
     */
    public function psr(): ?PsrRequest;

    /**
     * Définition du chemin absolu vers le répertoire racine de l'application.
     *
     * @param string
     *
     * @return static
     */
    public function setDocumentRoot(string $documentRoot): RequestInterface;
}