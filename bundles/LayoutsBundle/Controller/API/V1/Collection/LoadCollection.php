<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Serializer\Version;

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
