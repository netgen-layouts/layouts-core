<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;

use function array_values;

final class LayoutContext implements Context
{
    public function __construct(
        private LayoutService $layoutService,
        private LayoutTypeRegistry $layoutTypeRegistry,
    ) {}

    #[Given('/^there is a layout called "([^"]+)"$/')]
    public function thereIsALayoutCalled(string $layoutName): void
    {
        $this->createLayout($layoutName);
    }

    #[Given('/^there is a shared layout called "([^"]+)"$/')]
    public function thereIsASharedLayoutCalled(string $layoutName): void
    {
        $this->createLayout($layoutName, true);
    }

    private function createLayout(string $layoutName, bool $isShared = false): void
    {
        $createStruct = $this->layoutService->newLayoutCreateStruct(
            $this->getFirstLayoutType(),
            $layoutName,
            'en',
        );

        $createStruct->isShared = $isShared;

        $layoutDraft = $this->layoutService->createLayout($createStruct);

        $this->layoutService->publishLayout($layoutDraft);
    }

    private function getFirstLayoutType(): LayoutTypeInterface
    {
        $layoutTypes = array_values($this->layoutTypeRegistry->getLayoutTypes(true));

        return $layoutTypes[0];
    }
}
