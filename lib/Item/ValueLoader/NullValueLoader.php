<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item\ValueLoader;

use Netgen\Layouts\Item\ValueLoaderInterface;

final class NullValueLoader implements ValueLoaderInterface
{
    public function load(int|string $id): null
    {
        return null;
    }

    public function loadByRemoteId(int|string $remoteId): null
    {
        return null;
    }
}
