<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\EntityLoader;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Transfer\Output\EntityLoaderInterface;
use Ramsey\Uuid\Uuid;

final class RuleEntityLoader implements EntityLoaderInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    public function loadEntities(array $entityIds): iterable
    {
        foreach ($entityIds as $entityId) {
            try {
                yield $this->layoutResolverService->loadRule(Uuid::fromString($entityId));
            } catch (NotFoundException $e) {
                continue;
            }
        }
    }
}
