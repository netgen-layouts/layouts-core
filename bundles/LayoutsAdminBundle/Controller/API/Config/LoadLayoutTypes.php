<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\ArrayValue;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;

final class LoadLayoutTypes extends AbstractController
{
    private LayoutTypeRegistry $layoutTypeRegistry;

    public function __construct(LayoutTypeRegistry $layoutTypeRegistry)
    {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Serializes the layout types.
     */
    public function __invoke(): ArrayValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $layoutTypes = [];
        foreach ($this->layoutTypeRegistry->getLayoutTypes(true) as $layoutType) {
            $layoutTypes[] = new View($layoutType);
        }

        return new ArrayValue($layoutTypes);
    }
}
