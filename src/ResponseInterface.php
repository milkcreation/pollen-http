<?php

declare(strict_types=1);

namespace Pollen\Http;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * @mixin BaseResponse
 * @mixin ResponseTrait
 */
interface ResponseInterface
{
}