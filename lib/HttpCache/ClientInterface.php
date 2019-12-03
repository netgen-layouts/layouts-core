<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

interface ClientInterface
{
    /**
     * Purges all caches that have one of the provided tags.
     *
     * @param string[] $tags
     */
    public function purge(array $tags): void;

    /**
     * Commits the cache clear operations to the backend.
     */
    public function commit(): bool;
}
