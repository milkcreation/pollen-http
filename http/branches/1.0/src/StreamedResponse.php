<?php

declare(strict_types=1);

namespace Pollen\Http;

use Symfony\Component\HttpFoundation\StreamedResponse as BaseStreamedResponse;

class StreamedResponse extends BaseStreamedResponse implements StreamedResponseInterface
{
    use ResponseTrait;
}