<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Layout;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;

final class ZoneParamConverter extends ParamConverter
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

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
        $layout = $values['status'] === self::STATUS_PUBLISHED ?
            $this->layoutService->loadLayout(Uuid::fromString($values['layoutId'])) :
            $this->layoutService->loadLayoutDraft(Uuid::fromString($values['layoutId']));

        if (!$layout->hasZone($values['zoneIdentifier'])) {
            throw new NotFoundException('zone', $values['zoneIdentifier']);
        }

        return $layout->getZone($values['zoneIdentifier']);
    }
}
