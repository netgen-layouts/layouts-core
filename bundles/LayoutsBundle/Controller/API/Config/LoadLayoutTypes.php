<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\Config;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Serializer\Values\ArrayValue;
use Netgen\Layouts\Serializer\Values\View;

final class LoadLayoutTypes extends AbstractController
{
    /**
     * @var \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry
     */
    private $layoutTypeRegistry;

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
