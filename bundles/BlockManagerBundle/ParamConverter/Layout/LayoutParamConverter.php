<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

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

    public function getSourceAttributeNames()
    {
        return ['layoutId'];
    }

    public function getDestinationAttributeName()
    {
        return 'layout';
    }

    public function getSupportedClass()
    {
        return Layout::class;
    }

    public function loadValue(array $values)
    {
        if ($values['published']) {
            return $this->layoutService->loadLayout($values['layoutId']);
        }

        return $this->layoutService->loadLayoutDraft($values['layoutId']);
    }
}
