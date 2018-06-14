<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;

final class LoadItems extends Controller
{
    /**
     * Loads all collection items.
     */
    public function __invoke(Collection $collection): Value
    {
        $items = [];
        foreach ($collection->getItems() as $item) {
            $items[] = new VersionedValue($item, Version::API_V1);
        }

        return new Value($items);
    }
}
