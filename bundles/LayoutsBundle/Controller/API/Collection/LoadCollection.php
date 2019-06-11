<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Serializer\Values\Value;

final class LoadCollection extends AbstractController
{
    /**
     * Loads the collection.
     */
    public function __invoke(Collection $collection): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new Value($collection);
    }
}
