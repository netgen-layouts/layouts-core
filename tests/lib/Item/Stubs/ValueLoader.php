<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

use Netgen\Layouts\Item\ValueLoaderInterface;

final class ValueLoader implements ValueLoaderInterface
{
    public function __construct(
        private bool $exists,
    ) {}

    public function load(int|string $id): ?Value
    {
        return $this->exists ? new Value((int) $id, '') : null;
    }

    public function loadByRemoteId(int|string $remoteId): ?Value
    {
        return $this->exists ? new Value(0, (string) $remoteId) : null;
    }
}
