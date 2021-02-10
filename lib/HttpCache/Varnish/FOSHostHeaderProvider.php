<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache\Varnish;

final class FOSHostHeaderProvider implements HostHeaderProviderInterface
{
    /**
     * @var string[]
     */
    private array $servers;

    /**
     * @param string[] $servers
     */
    public function __construct(array $servers = [])
    {
        $this->servers = $servers;
    }

    public function provideHostHeader(): string
    {
        return $this->servers[0] ?? '';
    }
}
