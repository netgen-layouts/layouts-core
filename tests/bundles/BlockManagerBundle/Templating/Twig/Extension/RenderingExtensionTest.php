<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Exception;
use Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Twig_SimpleFunction;
use Twig_TokenParser;

class RenderingExtensionTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalVariableMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fragmentHandlerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->globalVariableMock = $this->createMock(GlobalVariable::class);
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $this->extension = new RenderingExtension(
            $this->globalVariableMock,
            $this->viewRendererMock,
            $this->fragmentHandlerMock,
            'ngbm_block:viewBlockById'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getName
     */
    public function testGetName()
    {
        $this->assertEquals(get_class($this->extension), $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getGlobals
     */
    public function testGetGlobals()
    {
        $this->assertEquals(
            array(
                'ngbm' => $this->globalVariableMock,
            ),
            $this->extension->getGlobals()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $this->assertNotEmpty($this->extension->getFunctions());

        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(Twig_SimpleFunction::class, $function);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::getTokenParsers
     */
    public function testGetTokenParsers()
    {
        $this->assertNotEmpty($this->extension->getTokenParsers());

        foreach ($this->extension->getTokenParsers() as $tokenParser) {
            $this->assertInstanceOf(Twig_TokenParser::class, $tokenParser);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::displayBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     */
    public function testDisplayBlockReturnsEmptyStringOnException()
    {
        $block = new Block(array('definition' => new BlockDefinition('block')));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception()));

        ob_start();

        $this->extension->displayBlock(
            $block,
            ViewInterface::CONTEXT_DEFAULT,
            $this->createMock(ContextualizedTwigTemplate::class)
        );

        $this->assertEquals('', ob_get_clean());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::displayBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     * @expectedException \Exception
     */
    public function testDisplayBlockThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);
        $block = new Block(array('definition' => new BlockDefinition('block')));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception()));

        $this->extension->displayBlock(
            $block,
            ViewInterface::CONTEXT_DEFAULT,
            $this->createMock(ContextualizedTwigTemplate::class)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     */
    public function testRenderItem()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->extension->renderItem(
                array(),
                new Item(),
                'view_type',
                array('param' => 'value'),
                ViewInterface::CONTEXT_DEFAULT
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     */
    public function testRenderItemReturnsEmptyStringOnException()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->extension->renderItem(
                array(),
                new Item(),
                'view_type',
                array('param' => 'value'),
                ViewInterface::CONTEXT_DEFAULT
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     * @expectedException \Exception
     */
    public function testRenderItemThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->extension->renderItem(
            array(),
            new Item(),
            'view_type',
            array('param' => 'value'),
            ViewInterface::CONTEXT_DEFAULT
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     */
    public function testRenderValueObject()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->extension->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_DEFAULT
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     */
    public function testRenderValueObjectWithNoContext()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->extension->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     */
    public function testRenderValueObjectWithContextInTwigContext()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->extension->renderValueObject(
                array(
                    'view_context' => ViewInterface::CONTEXT_DEFAULT,
                ),
                new Condition(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     */
    public function testRenderValueObjectReturnsEmptyStringOnException()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->extension->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_DEFAULT
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     * @expectedException \Exception
     */
    public function testRenderValueObjectThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(array('param' => 'value')),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT)
            )
            ->will($this->throwException(new Exception()));

        $this->extension->renderValueObject(
            array(),
            new Condition(),
            array('param' => 'value'),
            ViewInterface::CONTEXT_DEFAULT
        );
    }
}
