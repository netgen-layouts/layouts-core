<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Behat\Context\Admin;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Behat\Context\Transform\LayoutContext;
use Netgen\BlockManager\Behat\Page\Admin\SharedLayouts\IndexPage;
use Netgen\BlockManager\Behat\Page\App\IndexPage as AppIndexPage;
use Webmozart\Assert\Assert;

final class ManagingSharedLayoutsContext extends AdminContext
{
    /**
     * @var \Netgen\BlockManager\Behat\Page\Admin\SharedLayouts\IndexPage
     */
    private $indexPage;

    /**
     * @var \Netgen\BlockManager\Behat\Page\App\IndexPage
     */
    private $appPage;

    /**
     * @var \Netgen\BlockManager\Behat\Context\Transform\LayoutContext
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
    public function iCreateANewSharedLayout()
    {
        $this->indexPage->open();

        $this->indexPage->createLayout();
    }

    /**
     * @When /^I edit a (shared layout called "[^"]+")$/
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function iEditASharedLayout(Layout $layout)
    {
        $this->indexPage->open();

        $this->indexPage->editLayout($layout->getName());
    }

    /**
     * @When /^I click on a (shared layout called "[^"]+")$/
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function iClickOnASharedLayout(Layout $layout)
    {
        $this->indexPage->open();

        $this->indexPage->clickLayoutName($layout->getName());
    }

    /**
     * @When /^I duplicate a (shared layout called "[^"]+") with name "([^"]+)"$/
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $copiedLayoutName
     */
    public function iDuplicateASharedLayoutAndAccept(Layout $layout, $copiedLayoutName)
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
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function iDuplicateASharedLayoutAndCancel(Layout $layout)
    {
        $this->indexPage->open();

        $this->indexPage->openDuplicateLayoutModal($layout->getName());
        $this->indexPage->cancelModal();
    }

    /**
     * @When /^I delete a (shared layout called "[^"]+") and confirm deletion$/
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function iDeleteASharedLayoutAndAccept(Layout $layout)
    {
        $this->indexPage->open();

        $this->indexPage->openDeleteLayoutModal($layout->getName());
        $this->indexPage->submitModal();
    }

    /**
     * @When /^I delete a (shared layout called "[^"]+") and cancel deletion$/
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function iDeleteASharedLayoutAndCancel(Layout $layout)
    {
        $this->indexPage->open();

        $this->indexPage->openDeleteLayoutModal($layout->getName());
        $this->indexPage->cancelModal();
    }

    /**
     * @Then /^edit interface for (shared layout called "[^"]+") should open$/
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function editInterfaceShouldOpen(Layout $layout)
    {
        $this->appPage->verifyRoute();
        $this->appPage->verifyFragment('layout/' . $layout->getId());
        $this->appPage->verifyLayout($layout->getName());
    }

    /**
     * @Then /^interface for creating a new shared layout should open$/
     */
    public function editInterfaceForNewLayoutShouldOpen()
    {
        $this->appPage->verifyRoute();
        $this->appPage->verifyCreateForm(true);
    }

    /**
     * @Then /^a (shared layout called "[^"]+") should exist$/
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function sharedLayoutShouldExist(Layout $layout)
    {
        Assert::true($this->indexPage->layoutExists($layout->getName()));
    }

    /**
     * @Then /^a shared layout called "([^"]+)" should not exist$/
     *
     * @param string $layoutName
     */
    public function sharedLayoutShouldNotExist($layoutName)
    {
        Assert::false($this->layoutContext->hasLayoutWithName($layoutName));
        Assert::false($this->indexPage->layoutExists($layoutName));
    }

    public function iShouldGetAnError($errorMessage)
    {
        Assert::true($this->indexPage->modalErrorExists($errorMessage));
    }
}
