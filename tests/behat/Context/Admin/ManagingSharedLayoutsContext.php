<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Context\Admin;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Behat\Context\Transform\LayoutContext;
use Netgen\Layouts\Behat\Page\Admin\SharedLayouts\IndexPage;
use Netgen\Layouts\Behat\Page\App\IndexPage as AppIndexPage;
use Webmozart\Assert\Assert;

final class ManagingSharedLayoutsContext extends AdminContext
{
    /**
     * @var \Netgen\Layouts\Behat\Page\Admin\SharedLayouts\IndexPage
     */
    private $indexPage;

    /**
     * @var \Netgen\Layouts\Behat\Page\App\IndexPage
     */
    private $appPage;

    /**
     * @var \Netgen\Layouts\Behat\Context\Transform\LayoutContext
     */
    private $layoutContext;

    public function __construct(IndexPage $indexPage, AppIndexPage $appPage, LayoutContext $layoutContext)
    {
        $this->indexPage = $indexPage;
        $this->appPage = $appPage;
        $this->layoutContext = $layoutContext;
    }

    /**
     * @When /^I create a new shared layout$/
     */
    public function iCreateANewSharedLayout(): void
    {
        $this->indexPage->open();

        $this->indexPage->createLayout();
    }

    /**
     * @When /^I edit a (shared layout called "[^"]+")$/
     */
    public function iEditASharedLayout(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->editLayout($layout->getName());
    }

    /**
     * @When /^I click on a (shared layout called "[^"]+")$/
     */
    public function iClickOnASharedLayout(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->clickLayoutName($layout->getName());
    }

    /**
     * @When /^I duplicate a (shared layout called "[^"]+") with name "([^"]+)"$/
     */
    public function iDuplicateASharedLayoutAndAccept(Layout $layout, string $copiedLayoutName): void
    {
        $this->indexPage->open();

        $this->indexPage->openDuplicateLayoutModal($layout->getName());
        $this->indexPage->nameDuplicatedLayout($copiedLayoutName);

        $this->layoutContext->hasLayoutWithName($copiedLayoutName) ?
            $this->indexPage->submitModalWithError() :
            $this->indexPage->submitModal();
    }

    /**
     * @When /^I duplicate a (shared layout called "[^"]+") and cancel copying$/
     */
    public function iDuplicateASharedLayoutAndCancel(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->openDuplicateLayoutModal($layout->getName());
        $this->indexPage->cancelModal();
    }

    /**
     * @When /^I delete a (shared layout called "[^"]+") and confirm deletion$/
     */
    public function iDeleteASharedLayoutAndAccept(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->openDeleteLayoutModal($layout->getName());
        $this->indexPage->submitModal();
    }

    /**
     * @When /^I delete a (shared layout called "[^"]+") and cancel deletion$/
     */
    public function iDeleteASharedLayoutAndCancel(Layout $layout): void
    {
        $this->indexPage->open();

        $this->indexPage->openDeleteLayoutModal($layout->getName());
        $this->indexPage->cancelModal();
    }

    /**
     * @Then /^edit interface for (shared layout called "[^"]+") should open$/
     */
    public function editInterfaceShouldOpen(Layout $layout): void
    {
        $this->appPage->verifyRoute();
        $this->appPage->verifyFragment('layout/' . $layout->getId()->toString());
        $this->appPage->verifyLayout($layout->getName());
    }

    /**
     * @Then /^interface for creating a new shared layout should open$/
     */
    public function editInterfaceForNewLayoutShouldOpen(): void
    {
        $this->appPage->verifyRoute();
        $this->appPage->verifyCreateForm(true);
    }

    /**
     * @Then /^a (shared layout called "[^"]+") should exist$/
     */
    public function sharedLayoutShouldExist(Layout $layout): void
    {
        Assert::true($this->indexPage->layoutExists($layout->getName()));
    }

    /**
     * @Then /^a shared layout called "([^"]+)" should not exist$/
     */
    public function sharedLayoutShouldNotExist(string $layoutName): void
    {
        Assert::false($this->layoutContext->hasLayoutWithName($layoutName));
        Assert::false($this->indexPage->layoutExists($layoutName));
    }

    public function iShouldGetAnError(string $errorMessage): void
    {
        Assert::true($this->indexPage->modalErrorExists($errorMessage));
    }
}
