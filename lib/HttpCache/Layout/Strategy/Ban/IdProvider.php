<?php

namespace Netgen\BlockManager\HttpCache\Layout\Strategy\Ban;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\NotFoundException;

class IdProvider implements IdProviderInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Extracts all relevant IDs for a given layout.
     *
     * 1) If layout is shared, its ID and IDs of all reverse related layouts is returned.
     * 2) Otherwise, only the provided layout ID is returned.
     *
     * @param int|string $layoutId
     *
     * @return int[]|string[]
     */
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

        // @todo: Load all reverse related layouts and add their IDs to the list

        return $layoutIds;
    }
}
