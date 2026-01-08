<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Behat\Context\Admin;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Tests\App\Behat\Context\Transform\LayoutContext;
use Netgen\Layouts\Tests\App\Behat\Page\Admin\Layouts\IndexPage;
use Netgen\Layouts\Tests\App\Behat\Page\App\IndexPage as AppIndexPage;
use Zenstruck\Assert;

final class ManagingLayoutsContext implements Context
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

        $this->indexPage->editLayout($layout->name);
    }

    #[When('/^I click on a (layout called "[^"]+")$/')]
    public function iClickOnALayout(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->clickLayoutName($layout->name);
    }

    #[When('/^I duplicate a (layout called "[^"]+")$/')]
    public function iDuplicateALayout(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->openDuplicateLayoutModal($layout->name);
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

        $this->indexPage->openDeleteLayoutModal($layout->name);
    }

    #[Then('/^edit interface for (layout called "[^"]+") should open$/')]
    public function editInterfaceShouldOpen(Layout $layout): void
    {
        $this->appPage->verifyRoute();
        $this->appPage->verifyUrlFragment('layout/' . $layout->id->toString());
        $this->appPage->verifyLayout($layout->name);
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
        Assert::true($this->indexPage->layoutExists($layout->name), 'Layout with provided name does not exist');
    }

    #[Then('/^a layout called "([^"]+)" should not exist$/')]
    public function layoutShouldNotExist(string $layoutName): void
    {
        Assert::false($this->layoutContext->hasLayoutWithName($layoutName), 'Layout with provided name exists');
        Assert::false($this->indexPage->layoutExists($layoutName), 'Layout with provided name exists');
    }

    #[Then('/^there should be no error$/')]
    public function thereShouldBeNoError(): void
    {
        $this->indexPage->verifyModalErrorDoesNotExist();
    }

    #[Then('/^I should get an error saying "([^"]+)"$/')]
    public function iShouldGetAnError(string $errorMessage): void
    {
        Assert::true($this->indexPage->modalErrorExists($errorMessage), 'Modal error does not exist');
    }
}
