<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

final class LoadCollection extends Controller
{
    /**
     * Loads the collection.
     */
    public function __invoke(Collection $collection): VersionedValue
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');

        return new VersionedValue($collection, Version::API_V1);
    }
}
