<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;

final class LoadSharedLayouts extends AbstractController
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
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $layouts = [];
        foreach ($this->layoutService->loadSharedLayouts() as $layout) {
            $layouts[] = new VersionedValue($layout, Version::API_V1);
        }

        return new Value($layouts);
    }
}
