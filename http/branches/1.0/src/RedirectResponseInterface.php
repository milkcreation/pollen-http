<?php

declare(strict_types=1);

namespace Pollen\Http;

/**
 * @mixin \Symfony\Component\HttpFoundation\RedirectResponse
 * @mixin ResponseTrait
 */
interface RedirectResponseInterface extends ResponseInterface
{
}