<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;

final class LoadItem extends Controller
{
    /**
     * Loads the item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function __invoke(Item $item)
    {
        return new VersionedValue($item, Version::API_V1);
    }
}
