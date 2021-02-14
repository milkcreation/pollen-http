<?php

declare(strict_types=1);

namespace Pollen\Http;

use Symfony\Component\HttpFoundation\BinaryFileResponse as BaseBinaryFileResponse;

class BinaryFileResponse extends BaseBinaryFileResponse implements BinaryFileResponseInterface
{
    use ResponseTrait;
}