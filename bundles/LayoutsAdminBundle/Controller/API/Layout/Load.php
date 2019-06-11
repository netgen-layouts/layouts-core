<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Layout\Layout;

final class Load extends AbstractController
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

        return new View($layout);
    }
}
