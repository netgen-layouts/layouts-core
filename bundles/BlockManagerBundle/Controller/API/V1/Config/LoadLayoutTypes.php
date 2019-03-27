<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config;

use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

final class LoadLayoutTypes extends Controller
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function __construct(LayoutTypeRegistryInterface $layoutTypeRegistry)
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
