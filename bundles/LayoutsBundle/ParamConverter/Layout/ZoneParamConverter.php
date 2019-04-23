<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Layout;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Exception\NotFoundException;

final class ZoneParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
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
        $layout = $values['status'] === self::STATUS_PUBLISHED ?
            $this->layoutService->loadLayout($values['layoutId']) :
            $this->layoutService->loadLayoutDraft($values['layoutId']);

        if (!$layout->hasZone($values['zoneIdentifier'])) {
            throw new NotFoundException('zone', $values['zoneIdentifier']);
        }

        return $layout->getZone($values['zoneIdentifier']);
    }
}
