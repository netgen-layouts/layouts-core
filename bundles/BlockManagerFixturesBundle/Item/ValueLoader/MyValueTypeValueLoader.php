<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerFixturesBundle\Item\ValueLoader;

use Netgen\BlockManager\Item\ValueLoaderInterface;
use Netgen\Bundle\BlockManagerFixturesBundle\Item\Value;

final class MyValueTypeValueLoader implements ValueLoaderInterface
{
    public function load($id)
    {
        return new Value(['id' => (int) $id]);
    }

    public function loadByRemoteId($remoteId)
    {
        return new Value(['id' => (int) $remoteId]);
    }
}
