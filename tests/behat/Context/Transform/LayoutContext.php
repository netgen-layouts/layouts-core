<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\NotFoundException;

final class LayoutContext implements Context
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * @Transform /^layout called "([^"]+)"$/
     * @Transform :layout
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException
     */
    public function getLayoutByName(string $layoutName): Layout
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
     * @throws \Netgen\Layouts\Exception\NotFoundException
     */
    public function getSharedLayoutByName(string $layoutName): Layout
    {
        $layouts = $this->layoutService->loadSharedLayouts();

        foreach ($layouts as $layout) {
            if ($layout->getName() === $layoutName) {
                return $layout;
            }
        }

        throw new NotFoundException('layout', $layoutName);
    }

    public function hasLayoutWithName(string $layoutName): bool
    {
        return $this->layoutService->layoutNameExists($layoutName);
    }
}
