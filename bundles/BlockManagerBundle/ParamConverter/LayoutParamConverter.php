<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Returns source attribute name.
     *
     * @return string
     */
    public function getSourceAttributeName()
    {
        return 'layoutId';
    }

    /**
     * Returns destination attribute name.
     *
     * @return string
     */
    public function getDestinationAttributeName()
    {
        return 'layout';
    }

    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return Layout::class;
    }

    /**
     * Returns the value object.
     *
     * @param int|string $valueId
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function loadValueObject($valueId, $status)
    {
        return $this->layoutService->loadLayout($valueId, $status);
    }
}
