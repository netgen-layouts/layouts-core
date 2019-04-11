<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item\ValueLoader;

use Netgen\Layouts\Item\ValueLoaderInterface;

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
