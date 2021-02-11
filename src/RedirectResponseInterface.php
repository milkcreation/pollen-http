<?php

declare(strict_types=1);

namespace Pollen\Http;

use Symfony\Component\HttpFoundation\RedirectResponse as BaseRedirectResponse;

/**
 * @mixin BaseRedirectResponse
 */
interface RedirectResponseInterface extends ResponseInterface
{
}