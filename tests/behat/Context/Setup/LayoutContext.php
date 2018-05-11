<?php

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
     *
     * @param string $layoutName
     */
    public function thereIsALayoutCalled($layoutName)
    {
        $this->createLayout($layoutName);
    }

    /**
     * @Given /^there is a shared layout called "([^"]+)"$/
     *
     * @param string $layoutName
     */
    public function thereIsASharedLayoutCalled($layoutName)
    {
        $this->createLayout($layoutName, null, 'en', true);
    }

    /**
     * @param string $layoutName
     * @param \Netgen\BlockManager\Layout\Type\LayoutTypeInterface|null $layoutType
     * @param string $mainLocale
     * @param bool $shared
     */
    private function createLayout($layoutName, LayoutTypeInterface $layoutType = null, $mainLocale = 'en', $shared = false)
    {
        $layoutType = $layoutType !== null ? $layoutType : $this->getFirstLayoutType();

        $createStruct = $this->layoutService->newLayoutCreateStruct($layoutType, $layoutName, $mainLocale);
        $createStruct->shared = $shared;

        $layoutDraft = $this->layoutService->createLayout($createStruct);

        $this->layoutService->publishLayout($layoutDraft);
    }

    /**
     * @return \Netgen\BlockManager\Layout\Type\LayoutTypeInterface
     */
    private function getFirstLayoutType()
    {
        $layoutTypes = array_values($this->layoutTypeRegistry->getLayoutTypes(true));

        return $layoutTypes[0];
    }
}
