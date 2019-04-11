<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Value;
use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;

final class LayoutParamConverter extends ParamConverter
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
        return ['layoutId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'layout';
    }

    public function getSupportedClass(): string
    {
        return Layout::class;
    }

    public function loadValue(array $values): Value
    {
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutService->loadLayout($values['layoutId']);
        }

        if ($values['status'] === self::STATUS_ARCHIVED) {
            return $this->layoutService->loadLayoutArchive($values['layoutId']);
        }

        return $this->layoutService->loadLayoutDraft($values['layoutId']);
    }
}
