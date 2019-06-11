<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Serializer\Values\ArrayValue;
use Netgen\Layouts\Serializer\Values\Value;

final class LoadSharedLayouts extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

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
