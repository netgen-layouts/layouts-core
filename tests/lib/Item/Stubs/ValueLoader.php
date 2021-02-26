<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

use Netgen\Layouts\Item\ValueLoaderInterface;

final class ValueLoader implements ValueLoaderInterface
{
    private bool $exists;

    public function __construct(bool $exists)
    {
        $this->exists = $exists;
    }

    public function load($id): ?Value
    {
        return $this->exists ? new Value((int) $id, '') : null;
    }

    public function loadByRemoteId($remoteId): ?Value
    {
        return $this->exists ? new Value(0, (string) $remoteId) : null;
    }
}
