<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Collection\Item;

final class LoadItem extends AbstractController
{
    /**
     * Loads the item.
     */
    public function __invoke(Item $item): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new Value($item);
    }
}
