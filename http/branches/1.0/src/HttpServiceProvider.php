<?php

declare(strict_types=1);

namespace Pollen\Http;

use Pollen\Container\ServiceProvider;
use Psr\Http\Message\ServerRequestInterface as PsrRequestInterface;

class HttpServiceProvider extends ServiceProvider
{
    protected $provides = [
        PsrRequestInterface::class,
        RequestInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(RequestInterface::class, function () {
            return Request::getFromGlobals();
        });

        $this->getContainer()->share(PsrRequestInterface::class, function () {
            return Request::createPsr();
        });
    }
}
