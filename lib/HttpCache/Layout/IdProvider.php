<?php

namespace Netgen\BlockManager\HttpCache\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\NotFoundException;

/**
 * Extracts all relevant IDs for a given layout.
 *
 * 1) If layout is shared, its ID and IDs of all reverse related layouts is returned.
 * 2) Otherwise, only the provided layout ID is returned.
 */
final class IdProvider implements IdProviderInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    public function provideIds($layoutId)
    {
        $layoutIds = array($layoutId);

        try {
            $layout = $this->layoutService->loadLayout($layoutId);
        } catch (NotFoundException $e) {
            return $layoutIds;
        }

        if (!$layout->isShared()) {
            return $layoutIds;
        }

        $relatedLayouts = $this->layoutService->loadRelatedLayouts($layout);
        foreach ($relatedLayouts as $relatedLayout) {
            $layoutIds[] = $relatedLayout->getId();
        }

        return $layoutIds;
    }
}
