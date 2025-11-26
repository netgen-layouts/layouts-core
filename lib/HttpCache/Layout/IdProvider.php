<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache\Layout;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;

use function array_map;

/**
 * Extracts all relevant UUIDs for a given layout.
 *
 * 1) If layout is shared, its UUID and UUIDs of all reverse related layouts is returned.
 * 2) Otherwise, only the provided layout UUID is returned.
 */
final class IdProvider implements IdProviderInterface
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    public function provideIds(string $layoutId): array
    {
        $layoutIds = [$layoutId];

        try {
            $layout = $this->layoutService->loadLayout(Uuid::fromString($layoutId));
        } catch (NotFoundException) {
            return $layoutIds;
        }

        if (!$layout->shared) {
            return $layoutIds;
        }

        $relatedLayouts = $this->layoutService->loadRelatedLayouts($layout);

        return [
            ...$layoutIds,
            ...array_map('strval', $relatedLayouts->getLayoutIds()),
        ];
    }
}
