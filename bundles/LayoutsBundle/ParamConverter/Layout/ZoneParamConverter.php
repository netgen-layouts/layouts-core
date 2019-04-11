<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\API\Values\Value;
use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;

final class ZoneParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

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

    public function loadValue(array $values): Value
    {
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutService->loadZone($values['layoutId'], $values['zoneIdentifier']);
        }

        return $this->layoutService->loadZoneDraft($values['layoutId'], $values['zoneIdentifier']);
    }
}
