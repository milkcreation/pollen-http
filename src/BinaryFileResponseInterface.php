<?php

declare(strict_types=1);

namespace Pollen\Http;

/**
 * @mixin \Symfony\Component\HttpFoundation\BinaryFileResponse
 * @mixin ResponseTrait
 */
interface BinaryFileResponseInterface extends ResponseInterface
{
}