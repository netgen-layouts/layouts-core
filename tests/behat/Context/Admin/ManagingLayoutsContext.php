<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Context\Admin;

use Behat\Step\Then;
use Behat\Step\When;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Behat\Context\Transform\LayoutContext;
use Netgen\Layouts\Behat\Page\Admin\Layouts\IndexPage;
use Netgen\Layouts\Behat\Page\App\IndexPage as AppIndexPage;
use Zenstruck\Assert;

final class ManagingLayoutsContext extends AdminContext
{
    public function __construct(
        private IndexPage $indexPage,
        private AppIndexPage $appPage,
        private LayoutContext $layoutContext,
    ) {}

    #[When('/^I create a new layout$/')]
    public function iCreateANewLayout(): void
    {
        $this->indexPage->open();

        $this->indexPage->createLayout();
    }

    #[When('/^I edit a (layout called "[^"]+")$/')]
    public function iEditALayout(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->editLayout($layout->getName());
    }

    #[When('/^I click on a (layout called "[^"]+")$/')]
    public function iClickOnALayout(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->clickLayoutName($layout->getName());
    }

    #[When('/^I duplicate a (layout called "[^"]+")$/')]
    public function iDuplicateALayout(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->openDuplicateLayoutModal($layout->getName());
    }

    #[When('/^I set the layout name to "([^"]+)"$/')]
    public function iSetTheLayoutName(string $newName): void
    {
        $this->indexPage->nameLayout($newName);
    }

    #[When('/^I delete a (layout called "[^"]+")$/')]
    public function iDeleteALayout(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->openDeleteLayoutModal($layout->getName());
    }

    #[Then('/^edit interface for (layout called "[^"]+") should open$/')]
    public function editInterfaceShouldOpen(Layout $layout): void
    {
        $this->appPage->verifyRoute();
        $this->appPage->verifyUrlFragment('layout/' . $layout->getId()->toString());
        $this->appPage->verifyLayout($layout->getName());
    }

    #[Then('/^interface for creating a new layout should open$/')]
    public function editInterfaceForNewLayoutShouldOpen(): void
    {
        $this->appPage->verifyRoute();
        $this->appPage->verifyCreateForm();
    }

    #[When('/^I confirm the action$/')]
    public function iConfirmTheAction(): void
    {
        $this->indexPage->submitModal();
    }

    #[When('/^I cancel the action$/')]
    public function iCancelTheAction(): void
    {
        $this->indexPage->cancelModal();
    }

    #[Then('/^a (layout called "[^"]+") should exist$/')]
    public function layoutShouldExist(Layout $layout): void
    {
        Assert::true($this->indexPage->layoutExists($layout->getName()), 'Layout with provided name does not exist');
    }

    #[Then('/^a layout called "([^"]+)" should not exist$/')]
    public function layoutShouldNotExist(string $layoutName): void
    {
        Assert::false($this->layoutContext->hasLayoutWithName($layoutName), 'Layout with provided name exists');
        Assert::false($this->indexPage->layoutExists($layoutName), 'Layout with provided name exists');
    }

    public function thereShouldBeNoError(): void
    {
        $this->indexPage->verifyModalErrorDoesNotExist();
    }

    public function iShouldGetAnError(string $errorMessage): void
    {
        Assert::true($this->indexPage->modalErrorExists($errorMessage), 'Modal error does not exist');
    }
}
