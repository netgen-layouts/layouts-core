<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getPageLayout
     */
    public function testGetDefaultPageLayout()
    {
        self::assertNull($this->globalHelper->getPageLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::setPageLayout
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getPageLayout
     */
    public function testGetPageLayout()
    {
        $this->globalHelper->setPageLayout('defaultPagelayout');

        self::assertEquals('defaultPagelayout', $this->globalHelper->getPageLayout());
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
