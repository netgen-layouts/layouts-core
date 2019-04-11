<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Serializer\Values\Value;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Serializer\Version;

final class LoadItems extends AbstractController
{
    /**
     * Loads all collection items.
     */
    public function __invoke(Collection $collection): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $items = [];
        foreach ($collection->getItems() as $item) {
            $items[] = new VersionedValue($item, Version::API_V1);
        }

        return new Value($items);
    }
}
