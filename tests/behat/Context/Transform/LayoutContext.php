<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Behat\Transformation\Transform;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\NotFoundException;

final class LayoutContext implements Context
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    /**
     * @throws \Netgen\Layouts\Exception\NotFoundException
     */
    #[Transform('/^layout called "([^"]+)"$/')]
    #[Transform(':layout')]
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
     * @throws \Netgen\Layouts\Exception\NotFoundException
     */
    #[Transform('/^shared layout called "([^"]+)"$/')]
    #[Transform(':sharedLayout')]
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
