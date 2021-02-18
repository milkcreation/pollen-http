<?php

declare(strict_types=1);

namespace Pollen\Http;

/**
 * @mixin \Symfony\Component\HttpFoundation\StreamedResponse
 * @mixin ResponseTrait
 */
interface StreamedResponseInterface extends ResponseInterface
{
}