<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;

final class LoadItem extends AbstractController
{
    /**
     * Loads the item.
     */
    public function __invoke(Item $item): VersionedValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new VersionedValue($item, Version::API_V1);
    }
}
