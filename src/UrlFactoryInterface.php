<?php

declare(strict_types=1);

namespace Pollen\Http;

use Psr\Http\Message\UriInterface;
use League\Uri\Contracts\UriInterface as LeagueUri;

interface UrlFactoryInterface
{
    /**
     * Résolution de sortie sous forme de chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Ajout d'une portion de chemin à la fin de l'url.
     *
     * @param string $segment Portion de chemin à ajouter.
     *
     * @return static
     */
    public function appendSegment(string $segment): UrlFactoryInterface;

    /**
     * Suppression d'une portion de chemin de l'url.
     *
     * @param string $segment Portion de chemin à supprimer.
     *
     * @return static
     */
    public function deleteSegment(string $segment): UrlFactoryInterface;

    /**
     * Récupération de la chaîne encodée de l'url.
     *
     * @return LeagueUri|UriInterface|null
     */
    public function get();

    /**
     * Retourne la chaîne décodée de l'url.
     *
     * @param boolean $raw Activation de la sortie brute.
     *
     * @return string
     */
    public function decoded(bool $raw = true): string;

    /**
     * Récupération de paramètres de l'url.
     *
     * @param string|null $key Clé d'indice du paramètre. Tous si null.
     * @param string|null $default Valeur de retour par défaut de récupération d'un paramètre unique.
     *
     * @return array|string|null
     */
    public function params(?string $key = null, ?string $default = null);

    /**
     * Récupération du chemin relatif
     *
     * @return string|null
     */
    public function path(): ?string;

    /**
     * Définition de l'url.
     *
     * @param string|UriInterface|LeagueUri $uri
     *
     * @return static
     */
    public function set($uri): UrlFactoryInterface;

    /**
     * Ajout d'arguments à l'url.
     *
     * @param array $args Liste des arguments de requête à inclure.
     *
     * @return static
     */
    public function with(array $args): UrlFactoryInterface;

    /**
     * Ajout|Remplacement|Suppression du fragment (ancre).
     *
     * @param string $fragment
     *
     * @return static
     */
    public function withFragment(string $fragment): UrlFactoryInterface;

    /**
     * Suppression d'arguments de l'url.
     *
     * @param string[] $args Liste des arguments de requête à exclure.
     *
     * @return static
     */
    public function without(array $args): UrlFactoryInterface;

    /**
     * Récupération du rendu de l'url
     *
     * @return string
     */
    public function render(): string;
}