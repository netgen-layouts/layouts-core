<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\AbstractController;

final class LoadCollection extends AbstractController
{
    /**
     * Loads the collection.
     */
    public function __invoke(Collection $collection): VersionedValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new VersionedValue($collection, Version::API_V1);
    }
}
