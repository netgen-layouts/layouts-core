<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

final class NullClient implements ClientInterface
{
    public function purge(array $tags): void {}

    public function commit(): bool
    {
        return true;
    }
}
