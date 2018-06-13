<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item\ValueLoader;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;

final class NullValueLoader implements ValueLoaderInterface
{
    public function load($id)
    {
        throw new ItemException(
                sprintf('Item with ID "%s" could not be loaded.', $id)
            );
    }

    public function loadByRemoteId($remoteId)
    {
        throw new ItemException(
            sprintf('Item with remote ID "%s" could not be loaded.', $remoteId)
        );
    }
}
