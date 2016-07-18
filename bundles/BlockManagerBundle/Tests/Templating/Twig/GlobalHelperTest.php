<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use PHPUnit\Framework\TestCase;

class GlobalHelperTest extends TestCase
{
    protected $configMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper
     */
    protected $globalHelper;

    public function setUp()
    {
        $this->configMock = $this->createMock(ConfigurationInterface::class);

        $this->globalHelper = new GlobalHelper($this->configMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetDefaultLayoutView()
    {
        self::assertNull($this->globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::setLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetLayoutView()
    {
        $layoutView = new LayoutView(new Layout());
        $this->globalHelper->setLayoutView($layoutView);

        self::assertEquals($layoutView, $this->globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getPageLayoutTemplate
     */
    public function testGetDefaultPageLayoutTemplate()
    {
        self::assertNull($this->globalHelper->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::setPageLayoutTemplate
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate()
    {
        $this->globalHelper->setPageLayoutTemplate('defaultPagelayout');

        self::assertEquals('defaultPagelayout', $this->globalHelper->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayout
     */
    public function testGetLayout()
    {
        $layoutView = new LayoutView(new Layout());
        $this->globalHelper->setLayoutView($layoutView);

        self::assertEquals(new Layout(), $this->globalHelper->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayout
     */
    public function testGetLayoutWithNoLayoutView()
    {
        self::assertNull($this->globalHelper->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutTemplate
     */
    public function testGetLayoutTemplate()
    {
        $this->globalHelper->setPageLayoutTemplate('pagelayout.html.twig');

        $layoutView = new LayoutView(new Layout());
        $layoutView->setTemplate('layout.html.twig');
        $this->globalHelper->setLayoutView($layoutView);

        self::assertEquals('layout.html.twig', $this->globalHelper->getLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithNoLayoutView()
    {
        $this->globalHelper->setPageLayoutTemplate('pagelayout.html.twig');

        self::assertEquals('pagelayout.html.twig', $this->globalHelper->getLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getConfig
     */
    public function testGetConfig()
    {
        self::assertEquals($this->configMock, $this->globalHelper->getConfig());
    }
}
