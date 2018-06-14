<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Exception;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Template;

final class RenderingRuntimeTest extends TestCase
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
     * @var \Netgen\BlockManager\Tests\Stubs\ErrorHandler
     */
    private $errorHandler;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime
     */
    private $runtime;

    public function setUp(): void
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);
        $this->rendererMock = $this->createMock(RendererInterface::class);
        $this->localeProviderMock = $this->createMock(LocaleProviderInterface::class);
        $this->errorHandler = new ErrorHandler();

        $this->runtime = new RenderingRuntime(
            $this->blockServiceMock,
            $this->rendererMock,
            $this->localeProviderMock,
            new RequestStack(),
            $this->errorHandler
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlock(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    [
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    ]
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->runtime->renderBlock(
                [
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ],
                new Block(),
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockWithoutTwigTemplate(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    [
                        'param' => 'value',
                        'twig_template' => null,
                    ]
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->runtime->renderBlock(
                [],
                new Block(),
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockWithViewContext(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    [
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    ]
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->runtime->renderBlock(
                [
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ],
                new Block(),
                ['param' => 'value'],
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockWithViewContextFromTwigContext(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Block()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    [
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    ]
                )
            )
            ->will($this->returnValue('rendered block'));

        $this->assertEquals(
            'rendered block',
            $this->runtime->renderBlock(
                [
                    'view_context' => ViewInterface::CONTEXT_API,
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ],
                new Block(),
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockReturnsEmptyStringOnException(): void
    {
        $block = new Block(['definition' => new BlockDefinition()]);

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->will($this->throwException(new Exception()));

        $renderedBlock = $this->runtime->renderBlock(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ],
            $block
        );

        $this->assertEquals('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderBlockThrowsExceptionInDebug(): void
    {
        $this->errorHandler->setThrow(true);
        $block = new Block(['definition' => new BlockDefinition()]);

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->runtime->renderBlock(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ],
            $block
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholder(): void
    {
        $block = new Block(
            [
                'placeholders' => [
                    'main' => new Placeholder(),
                ],
            ]
        );

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    [
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    ]
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                [
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ],
                $block,
                'main',
                [
                    'block' => $block,
                    'param' => 'value',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderWithoutTwigTemplate(): void
    {
        $block = new Block(
            [
                'placeholders' => [
                    'main' => new Placeholder(),
                ],
            ]
        );

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(
                    [
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => null,
                    ]
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                [],
                $block,
                'main',
                [
                    'block' => $block,
                    'param' => 'value',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderWithViewContext(): void
    {
        $block = new Block(
            [
                'placeholders' => [
                    'main' => new Placeholder(),
                ],
            ]
        );

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    [
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    ]
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                [
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ],
                $block,
                'main',
                [
                    'block' => $block,
                    'param' => 'value',
                ],
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderWithViewContextFromTwigContext(): void
    {
        $block = new Block(
            [
                'placeholders' => [
                    'main' => new Placeholder(),
                ],
            ]
        );

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Placeholder()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(
                    [
                        'block' => $block,
                        'param' => 'value',
                        'twig_template' => new ContextualizedTwigTemplate(
                            $this->createMock(Template::class)
                        ),
                    ]
                )
            )
            ->will($this->returnValue('rendered placeholder'));

        $this->assertEquals(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                [
                    'view_context' => ViewInterface::CONTEXT_API,
                    'twig_template' => new ContextualizedTwigTemplate(
                        $this->createMock(Template::class)
                    ),
                ],
                $block,
                'main',
                [
                    'block' => $block,
                    'param' => 'value',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderReturnsEmptyStringOnException(): void
    {
        $block = new Block(['placeholders' => ['main' => new Placeholder()]]);

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->will($this->throwException(new Exception()));

        $renderedBlock = $this->runtime->renderPlaceholder(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ],
            $block,
            'main'
        );

        $this->assertEquals('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderPlaceholderThrowsExceptionInDebug(): void
    {
        $this->errorHandler->setThrow(true);
        $block = new Block(['placeholders' => ['main' => new Placeholder()]]);

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->runtime->renderPlaceholder(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ],
            $block,
            'main'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItem(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->runtime->renderItem(
                [],
                new Item(),
                'view_type',
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemWithViewContext(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->runtime->renderItem(
                [],
                new Item(),
                'view_type',
                ['param' => 'value'],
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemWithViewContextFromTwigContext(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Item()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->runtime->renderItem(
                [
                    'view_context' => ViewInterface::CONTEXT_API,
                ],
                new Item(),
                'view_type',
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemReturnsEmptyStringOnException(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Item(['valueType' => 'value_type'])),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->runtime->renderItem(
                [],
                new Item(['valueType' => 'value_type']),
                'view_type',
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderItemThrowsExceptionInDebug(): void
    {
        $this->errorHandler->setThrow(true);

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Item(['valueType' => 'value_type'])),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->will($this->throwException(new Exception('Test exception text')));

        $this->runtime->renderItem(
            [],
            new Item(['valueType' => 'value_type']),
            'view_type',
            ['param' => 'value']
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValue(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(['param' => 'value'])
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->runtime->renderValue(
                [],
                new Condition(),
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueWithViewContext(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(['param' => 'value'])
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->runtime->renderValue(
                [],
                new Condition(),
                ['param' => 'value'],
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueWithContextFromTwigContext(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_API),
                $this->equalTo(['param' => 'value'])
            )
            ->will($this->returnValue('rendered value'));

        $this->assertEquals(
            'rendered value',
            $this->runtime->renderValue(
                [
                    'view_context' => ViewInterface::CONTEXT_API,
                ],
                new Condition(),
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueReturnsEmptyStringOnException(): void
    {
        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(['param' => 'value'])
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals(
            '',
            $this->runtime->renderValue(
                [],
                new Condition(),
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderValueThrowsExceptionInDebug(): void
    {
        $this->errorHandler->setThrow(true);

        $this->rendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo(new Condition()),
                $this->equalTo(ViewInterface::CONTEXT_DEFAULT),
                $this->equalTo(['param' => 'value'])
            )
            ->will($this->throwException(new Exception('Test exception text')));

        $this->runtime->renderValue(
            [],
            new Condition(),
            ['param' => 'value']
        );
    }
}
