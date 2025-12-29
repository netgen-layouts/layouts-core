<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Exception\NotFoundException;
use Symfony\Component\Uid\Uuid;

final class ZoneValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    protected function getSourceAttributeNames(): array
    {
        return ['layoutId', 'zoneIdentifier'];
    }

    protected function getDestinationAttributeName(): string
    {
        return 'zone';
    }

    protected function getSupportedClass(): string
    {
        return Zone::class;
    }

    protected function loadValue(array $parameters): Zone
    {
        $layout = match ($parameters['status']) {
            Status::Published => $this->layoutService->loadLayout(Uuid::fromString($parameters['layoutId'])),
            default => $this->layoutService->loadLayoutDraft(Uuid::fromString($parameters['layoutId'])),
        };

        if (!$layout->hasZone($parameters['zoneIdentifier'])) {
            throw new NotFoundException('zone', $parameters['zoneIdentifier']);
        }

        return $layout->getZone($parameters['zoneIdentifier']);
    }
}
