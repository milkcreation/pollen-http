<?php

declare(strict_types=1);

namespace Pollen\Http;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse implements JsonResponseInterface
{
    use ResponseTrait;
}