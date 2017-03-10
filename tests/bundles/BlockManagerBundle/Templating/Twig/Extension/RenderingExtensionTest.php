<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Exception;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
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
    protected $blockServiceMock;

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
        $this->blockServiceMock = $this->createMock(BlockService::class);
        $this->globalVariableMock = $this->createMock(GlobalVariable::class);
        $this->viewRendererMock = $this->createMock(RendererInterface::class);
        $this->fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $this->extension = new RenderingExtension(
            $this->blockServiceMock,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     */
    public function testRenderBlock()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    array(
                        'param' => 'value',
                        'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                    )
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->extension->renderBlock(
                array(
                    'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                ),
                new Block(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     */
    public function testRenderBlockWithoutTwigTemplate()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    array(
                        'param' => 'value',
                        'twig_template' => null,
                    )
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->extension->renderBlock(
                array(),
                new Block(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     */
    public function testRenderBlockWithViewContext()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    array(
                        'param' => 'value',
                        'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                    )
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->extension->renderBlock(
                array(
                    'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                ),
                new Block(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     */
    public function testRenderBlockWithViewContextFromTwigContext()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    array(
                        'param' => 'value',
                        'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                    )
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->extension->renderBlock(
                array(
                    'view_context' => ViewInterface::CONTEXT_API,
                    'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                ),
                new Block(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     */
    public function testRenderBlockReturnsEmptyStringOnException()
    {
        $block = new Block(array('definition' => new BlockDefinition('block')));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception()));

        $renderedBlock = $this->extension->renderBlock(
            array(
                'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
            ),
            $block
        );

        $this->assertEquals('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderBlockThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);
        $block = new Block(array('definition' => new BlockDefinition('block')));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->extension->renderBlock(
            array(
                'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
            ),
            $block
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderPlaceholder
     */
    public function testRenderPlaceholder()
    {
        $block = new Block(
            array(
                'placeholders' => array(
                    'main' => new Placeholder(),
                ),
            )
        );

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    array(
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                    )
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->extension->renderPlaceholder(
                array(
                    'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                ),
                $block,
                'main',
                array(
                    'block' => $block,
                    'param' => 'value',
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderPlaceholder
     */
    public function testRenderPlaceholderWithoutTwigTemplate()
    {
        $block = new Block(
            array(
                'placeholders' => array(
                    'main' => new Placeholder(),
                ),
            )
        );

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    array(
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => null,
                    )
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->extension->renderPlaceholder(
                array(),
                $block,
                'main',
                array(
                    'block' => $block,
                    'param' => 'value',
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderPlaceholder
     */
    public function testRenderPlaceholderWithViewContext()
    {
        $block = new Block(
            array(
                'placeholders' => array(
                    'main' => new Placeholder(),
                ),
            )
        );

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    array(
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                    )
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->extension->renderPlaceholder(
                array(
                    'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                ),
                $block,
                'main',
                array(
                    'block' => $block,
                    'param' => 'value',
                ),
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderPlaceholder
     */
    public function testRenderPlaceholderWithViewContextFromTwigContext()
    {
        $block = new Block(
            array(
                'placeholders' => array(
                    'main' => new Placeholder(),
                ),
            )
        );

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    array(
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                    )
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->extension->renderPlaceholder(
                array(
                    'view_context' => ViewInterface::CONTEXT_API,
                    'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
                ),
                $block,
                'main',
                array(
                    'block' => $block,
                    'param' => 'value',
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderPlaceholder
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     */
    public function testRenderPlaceholderReturnsEmptyStringOnException()
    {
        $block = new Block(array('placeholders' => array('main' => new Placeholder())));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception()));

        $renderedBlock = $this->extension->renderPlaceholder(
            array(
                'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
            ),
            $block,
            'main'
        );

        $this->assertEquals('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderPlaceholder
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderPlaceholderThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);
        $block = new Block(array('placeholders' => array('main' => new Placeholder())));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->extension->renderPlaceholder(
            array(
                'twig_template' => $this->createMock(ContextualizedTwigTemplate::class),
            ),
            $block,
            'main'
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
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value'))
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->extension->renderItem(
                array(),
                new Item(),
                'view_type',
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     */
    public function testRenderItemWithViewContext()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value'))
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->extension->renderItem(
                array(),
                new Item(),
                'view_type',
                array('param' => 'value'),
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     */
    public function testRenderItemWithViewContextFromTwigContext()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value'))
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->extension->renderItem(
                array(
                    'view_context' => ViewInterface::CONTEXT_API,
                ),
                new Item(),
                'view_type',
                array('param' => 'value')
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
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value'))
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->extension->renderItem(
                array(),
                new Item(),
                'view_type',
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderItemThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value'))
            )
            ->will($this->throwException(new Exception('Test exception text')));

        $this->extension->renderItem(
            array(),
            new Item(),
            'view_type',
            array('param' => 'value')
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
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(array('param' => 'value'))
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
    public function testRenderValueObjectWithViewContext()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(array('param' => 'value'))
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->extension->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     */
    public function testRenderValueObjectWithContextFromTwigContext()
    {
        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(array('param' => 'value'))
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->extension->renderValueObject(
                array(
                    'view_context' => ViewInterface::CONTEXT_API,
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
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(array('param' => 'value'))
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->extension->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension::handleException
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderValueObjectThrowsExceptionInDebug()
    {
        $this->extension->setDebug(true);

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(array('param' => 'value'))
            )
            ->will($this->throwException(new Exception('Test exception text')));

        $this->extension->renderValueObject(
            array(),
            new Condition(),
            array('param' => 'value')
        );
    }
}
