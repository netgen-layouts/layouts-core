<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Config;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Serializer\Values\Value;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;

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
    public function __invoke(): Value
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $layoutTypes = [];
        foreach ($this->layoutTypeRegistry->getLayoutTypes(true) as $layoutType) {
            $layoutTypes[] = new View($layoutType, Version::API_V1);
        }

        return new Value($layoutTypes);
    }
}
