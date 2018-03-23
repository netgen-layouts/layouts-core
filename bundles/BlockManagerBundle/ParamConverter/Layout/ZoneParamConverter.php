<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

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

    public function getSourceAttributeNames()
    {
        return array('layoutId', 'zoneIdentifier');
    }

    public function getDestinationAttributeName()
    {
        return 'zone';
    }

    public function getSupportedClass()
    {
        return Zone::class;
    }

    public function loadValue(array $values)
    {
        if ($values['published']) {
            return $this->layoutService->loadZone($values['layoutId'], $values['zoneIdentifier']);
        }

        return $this->layoutService->loadZoneDraft($values['layoutId'], $values['zoneIdentifier']);
    }
}
