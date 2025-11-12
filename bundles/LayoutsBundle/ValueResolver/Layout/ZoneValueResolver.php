<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;

final class ZoneValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    public function getSourceAttributeNames(): array
    {
        return ['layoutId', 'zoneIdentifier'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'zone';
    }

    public function getSupportedClass(): string
    {
        return Zone::class;
    }

    public function loadValue(array $values): Zone
    {
        $layout = match ($values['status']) {
            self::STATUS_PUBLISHED => $this->layoutService->loadLayout(Uuid::fromString($values['layoutId'])),
            default => $this->layoutService->loadLayoutDraft(Uuid::fromString($values['layoutId'])),
        };

        if (!$layout->hasZone($values['zoneIdentifier'])) {
            throw new NotFoundException('zone', $values['zoneIdentifier']);
        }

        return $layout->getZone($values['zoneIdentifier']);
    }
}
