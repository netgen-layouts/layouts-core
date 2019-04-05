<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item\ValueLoader;

use Netgen\BlockManager\Item\ValueLoaderInterface;

final class NullValueLoader implements ValueLoaderInterface
{
    public function load($id): ?object
    {
        return null;
    }

    public function loadByRemoteId($remoteId): ?object
    {
        return null;
    }
}
