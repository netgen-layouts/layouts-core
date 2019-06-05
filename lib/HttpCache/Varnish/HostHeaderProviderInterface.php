<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache\Varnish;

interface HostHeaderProviderInterface
{
    /**
     * Provides the value of the "Host" header used by the Varnish client.
     *
     * Abstracted due to different implementations by different backends.
     */
    public function provideHostHeader(): string;
}
