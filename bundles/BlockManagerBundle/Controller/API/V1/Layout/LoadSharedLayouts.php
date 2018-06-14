<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;

final class LoadSharedLayouts extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Loads all shared layouts.
     */
    public function __invoke(): Value
    {
        $layouts = [];
        foreach ($this->layoutService->loadSharedLayouts() as $layout) {
            $layouts[] = new VersionedValue($layout, Version::API_V1);
        }

        return new Value($layouts);
    }
}
