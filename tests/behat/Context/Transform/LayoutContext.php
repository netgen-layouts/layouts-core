<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\NotFoundException;

final class LayoutContext implements Context
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * @Transform /^layout called "([^"]+)"$/
     * @Transform :layout
     *
     * @param string $layoutName
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function getLayoutByName($layoutName)
    {
        $layouts = $this->layoutService->loadLayouts();

        foreach ($layouts as $layout) {
            if ($layout->getName() === $layoutName) {
                return $layout;
            }
        }

        throw new NotFoundException('layout', $layoutName);
    }

    /**
     * @Transform /^shared layout called "([^"]+)"$/
     * @Transform :sharedLayout
     *
     * @param string $layoutName
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function getSharedLayoutByName($layoutName)
    {
        $layouts = $this->layoutService->loadSharedLayouts();

        foreach ($layouts as $layout) {
            if ($layout->getName() === $layoutName) {
                return $layout;
            }
        }

        throw new NotFoundException('layout', $layoutName);
    }

    /**
     * @param string $layoutName
     *
     * @return bool
     */
    public function hasLayoutWithName($layoutName)
    {
        return $this->layoutService->layoutNameExists($layoutName);
    }
}
