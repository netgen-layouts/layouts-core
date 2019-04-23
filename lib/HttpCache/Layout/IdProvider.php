<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache\Layout;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;

/**
 * Extracts all relevant IDs for a given layout.
 *
 * 1) If layout is shared, its ID and IDs of all reverse related layouts is returned.
 * 2) Otherwise, only the provided layout ID is returned.
 */
final class IdProvider implements IdProviderInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    public function provideIds($layoutId): array
    {
        $layoutIds = [$layoutId];

        try {
            $layout = $this->layoutService->loadLayout(Uuid::fromString($layoutId));
        } catch (NotFoundException $e) {
            return $layoutIds;
        }

        if (!$layout->isShared()) {
            return $layoutIds;
        }

        $relatedLayouts = $this->layoutService->loadRelatedLayouts($layout);

        return array_merge(
            $layoutIds,
            array_map('strval', $relatedLayouts->getLayoutIds())
        );
    }
}
