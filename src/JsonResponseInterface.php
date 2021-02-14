<?php

declare(strict_types=1);

namespace Pollen\Http;

/**
 * @mixin \Symfony\Component\HttpFoundation\JsonResponse
 * @mixin ResponseTrait
 */
interface JsonResponseInterface extends ResponseInterface
{
}