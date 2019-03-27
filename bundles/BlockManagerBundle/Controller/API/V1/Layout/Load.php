<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

final class Load extends Controller
{
    /**
     * Loads either the draft status or published status of specified layout.
     *
     * If a query param "published" with value of "true" is provided, published
     * state will be loaded directly, without first loading the draft.
     */
    public function __invoke(Layout $layout): View
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new View($layout, Version::API_V1);
    }
}
