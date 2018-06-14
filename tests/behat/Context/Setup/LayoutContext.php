<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;

final class LayoutContext implements Context
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function __construct(LayoutService $layoutService, LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        $this->layoutService = $layoutService;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * @Given /^there is a layout called "([^"]+)"$/
     */
    public function thereIsALayoutCalled(string $layoutName): void
    {
        $this->createLayout($layoutName);
    }

    /**
     * @Given /^there is a shared layout called "([^"]+)"$/
     */
    public function thereIsASharedLayoutCalled(string $layoutName): void
    {
        $this->createLayout($layoutName, null, 'en', true);
    }

    private function createLayout(string $layoutName, LayoutTypeInterface $layoutType = null, string $mainLocale = 'en', bool $shared = false): void
    {
        $layoutType = $layoutType ?? $this->getFirstLayoutType();

        $createStruct = $this->layoutService->newLayoutCreateStruct($layoutType, $layoutName, $mainLocale);
        $createStruct->shared = $shared;

        $layoutDraft = $this->layoutService->createLayout($createStruct);

        $this->layoutService->publishLayout($layoutDraft);
    }

    private function getFirstLayoutType(): LayoutTypeInterface
    {
        $layoutTypes = array_values($this->layoutTypeRegistry->getLayoutTypes(true));

        return $layoutTypes[0];
    }
}
