<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\ArrayValue;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Collection\Collection;

final class LoadItems extends AbstractController
{
    /**
     * Loads all collection items.
     */
    public function __invoke(Collection $collection): ArrayValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $items = [];
        foreach ($collection->getItems() as $item) {
            $items[] = new Value($item);
        }

        return new ArrayValue($items);
    }
}
