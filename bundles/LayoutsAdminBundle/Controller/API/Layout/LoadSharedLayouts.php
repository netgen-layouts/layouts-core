<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\ArrayValue;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;

final class LoadSharedLayouts extends AbstractController
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Loads all shared layouts.
     */
    public function __invoke(): ArrayValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $layouts = [];
        foreach ($this->layoutService->loadSharedLayouts() as $layout) {
            $layouts[] = new Value($layout);
        }

        return new ArrayValue($layouts);
    }
}
