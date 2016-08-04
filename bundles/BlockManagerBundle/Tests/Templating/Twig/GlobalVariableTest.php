<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;

class GlobalVariableTest extends TestCase
{
    protected $configMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    public function setUp()
    {
        $this->configMock = $this->createMock(ConfigurationInterface::class);

        $this->globalVariable = new GlobalVariable($this->configMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetDefaultLayoutView()
    {
        $this->assertNull($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::setLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutView()
    {
        $layoutView = new LayoutView(new Layout());
        $this->globalVariable->setLayoutView($layoutView);

        $this->assertEquals($layoutView, $this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetDefaultPageLayoutTemplate()
    {
        $this->assertNull($this->globalVariable->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::setPageLayoutTemplate
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate()
    {
        $this->globalVariable->setPageLayoutTemplate('defaultPagelayout');

        $this->assertEquals('defaultPagelayout', $this->globalVariable->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayout()
    {
        $layoutView = new LayoutView(new Layout());
        $this->globalVariable->setLayoutView($layoutView);

        $this->assertEquals(new Layout(), $this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoLayoutView()
    {
        $this->assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplate()
    {
        $this->globalVariable->setPageLayoutTemplate('pagelayout.html.twig');

        $layoutView = new LayoutView(new Layout());
        $layoutView->setTemplate('layout.html.twig');
        $this->globalVariable->setLayoutView($layoutView);

        $this->assertEquals('layout.html.twig', $this->globalVariable->getLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithNoLayoutView()
    {
        $this->globalVariable->setPageLayoutTemplate('pagelayout.html.twig');

        $this->assertEquals('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals($this->configMock, $this->globalVariable->getConfig());
    }
}
