<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\EntityLoader;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Transfer\Output\EntityLoaderInterface;
use Ramsey\Uuid\Uuid;

final class LayoutEntityLoader implements EntityLoaderInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    public function loadEntities(array $entityIds): iterable
    {
        foreach ($entityIds as $entityId) {
            try {
                yield $this->layoutService->loadLayout(Uuid::fromString($entityId));
            } catch (NotFoundException $e) {
                continue;
            }
        }
    }
}
