<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache\Varnish;

final class FOSHostHeaderProvider implements HostHeaderProviderInterface
{
    /**
     * @param string[] $servers
     */
    public function __construct(
        private array $servers = [],
    ) {}

    public function provideHostHeader(): string
    {
        return $this->servers[0] ?? '';
    }
}
