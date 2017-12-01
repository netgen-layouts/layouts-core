<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Exception;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Template;

class RenderingRuntimeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $rendererMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $localeProviderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime
     */
    private $runtime;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);
        $this->rendererMock = $this->createMock(RendererInterface::class);
        $this->localeProviderMock = $this->createMock(LocaleProviderInterface::class);

        $this->runtime = new RenderingRuntime(
            $this->blockServiceMock,
            $this->rendererMock,
            $this->localeProviderMock,
            new RequestStack()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
     */
    public function testRenderBlock()
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    array(
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    )
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->runtime->renderBlock(
                array(
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ),
                new Block(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
     */
    public function testRenderBlockWithoutTwigTemplate()
    {
        $this->rendererMock
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
            $this->runtime->renderBlock(
                array(),
                new Block(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
     */
    public function testRenderBlockWithViewContext()
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    array(
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    )
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->runtime->renderBlock(
                array(
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ),
                new Block(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
     */
    public function testRenderBlockWithViewContextFromTwigContext()
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    array(
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    )
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->runtime->renderBlock(
                array(
                    'view_context' => ViewInterface::CONTEXT_API,
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ),
                new Block(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::handleException
     */
    public function testRenderBlockReturnsEmptyStringOnException()
    {
        $block = new Block(array('definition' => new BlockDefinition('block')));

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception()));

        $renderedBlock = $this->runtime->renderBlock(
            array(
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ),
            $block
        );

        $this->assertEquals('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::handleException
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderBlockThrowsExceptionInDebug()
    {
        $this->runtime->setDebug(true);
        $block = new Block(array('definition' => new BlockDefinition('block')));

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->runtime->renderBlock(
            array(
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ),
            $block
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
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

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    array(
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    )
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                array(
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
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

        $this->rendererMock
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
            $this->runtime->renderPlaceholder(
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
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

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    array(
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    )
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                array(
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
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

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    array(
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    )
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                array(
                    'view_context' => ViewInterface::CONTEXT_API,
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::handleException
     */
    public function testRenderPlaceholderReturnsEmptyStringOnException()
    {
        $block = new Block(array('placeholders' => array('main' => new Placeholder())));

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception()));

        $renderedBlock = $this->runtime->renderPlaceholder(
            array(
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ),
            $block,
            'main'
        );

        $this->assertEquals('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getTwigTemplate
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::handleException
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderPlaceholderThrowsExceptionInDebug()
    {
        $this->runtime->setDebug(true);
        $block = new Block(array('placeholders' => array('main' => new Placeholder())));

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->runtime->renderPlaceholder(
            array(
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ),
            $block,
            'main'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItem()
    {
        $this->rendererMock
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
            $this->runtime->renderItem(
                array(),
                new Item(),
                'view_type',
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemWithViewContext()
    {
        $this->rendererMock
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
            $this->runtime->renderItem(
                array(),
                new Item(),
                'view_type',
                array('param' => 'value'),
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemWithViewContextFromTwigContext()
    {
        $this->rendererMock
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
            $this->runtime->renderItem(
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::handleException
     */
    public function testRenderItemReturnsEmptyStringOnException()
    {
        $this->rendererMock
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
            $this->runtime->renderItem(
                array(),
                new Item(),
                'view_type',
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::handleException
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderItemThrowsExceptionInDebug()
    {
        $this->runtime->setDebug(true);

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(array('view_type' => 'view_type', 'param' => 'value'))
            )
            ->will($this->throwException(new Exception('Test exception text')));

        $this->runtime->renderItem(
            array(),
            new Item(),
            'view_type',
            array('param' => 'value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     */
    public function testRenderValueObject()
    {
        $this->rendererMock
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
            $this->runtime->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     */
    public function testRenderValueObjectWithViewContext()
    {
        $this->rendererMock
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
            $this->runtime->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value'),
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     */
    public function testRenderValueObjectWithContextFromTwigContext()
    {
        $this->rendererMock
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
            $this->runtime->renderValueObject(
                array(
                    'view_context' => ViewInterface::CONTEXT_API,
                ),
                new Condition(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::handleException
     */
    public function testRenderValueObjectReturnsEmptyStringOnException()
    {
        $this->rendererMock
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
            $this->runtime->renderValueObject(
                array(),
                new Condition(),
                array('param' => 'value')
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValueObject
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::setDebug
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::handleException
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderValueObjectThrowsExceptionInDebug()
    {
        $this->runtime->setDebug(true);

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValueObject')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(array('param' => 'value'))
            )
            ->will($this->throwException(new Exception('Test exception text')));

        $this->runtime->renderValueObject(
            array(),
            new Condition(),
            array('param' => 'value')
        );
    }
}
