<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;

final class VarnishClient implements ClientInterface
{
    public function __construct(
        private CacheInvalidator $fosInvalidator,
    ) {}

    public function purge(array $tags): void
    {
        $this->fosInvalidator->invalidateTags($tags);
    }

    public function commit(): bool
    {
        try {
            $this->fosInvalidator->flush();
        } catch (ExceptionCollection) {
            // Do nothing, FOS invalidator will write to log.
            return false;
        }

        return true;
    }
}
