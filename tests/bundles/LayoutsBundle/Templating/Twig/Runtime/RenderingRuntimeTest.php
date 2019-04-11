<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Exception;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\Tests\Stubs\ErrorHandler;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Template;
use Twig\TemplateWrapper;

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
     * @var \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime
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
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlock(): void
    {
        $block = new Block();
        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($block),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(
                    [
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ]
                )
            )
            ->willReturn('rendered block');

        self::assertSame(
            'rendered block',
            $this->runtime->renderBlock(
                [
                    'twig_template' => $twigTemplate,
                ],
                $block,
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockWithoutTwigTemplate(): void
    {
        $block = new Block();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($block),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(
                    [
                        'twig_template' => null,
                        'param' => 'value',
                    ]
                )
            )
            ->willReturn('rendered block');

        self::assertSame(
            'rendered block',
            $this->runtime->renderBlock(
                [],
                $block,
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockWithViewContext(): void
    {
        $block = new Block();
        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($block),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(
                    [
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ]
                )
            )
            ->willReturn('rendered block');

        self::assertSame(
            'rendered block',
            $this->runtime->renderBlock(
                [
                    'twig_template' => $twigTemplate,
                ],
                $block,
                ['param' => 'value'],
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockWithViewContextFromTwigContext(): void
    {
        $block = new Block();
        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($block),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(
                    [
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ]
                )
            )
            ->willReturn('rendered block');

        self::assertSame(
            'rendered block',
            $this->runtime->renderBlock(
                [
                    'view_context' => ViewInterface::CONTEXT_API,
                    'twig_template' => $twigTemplate,
                ],
                $block,
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockReturnsEmptyStringOnException(): void
    {
        $block = Block::fromArray(['definition' => new BlockDefinition()]);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->willThrowException(new Exception());

        $renderedBlock = $this->runtime->renderBlock(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ],
            $block
        );

        self::assertSame('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);
        $block = Block::fromArray(['definition' => new BlockDefinition()]);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

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
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholder(): void
    {
        $placeholder = new Placeholder();
        $block = Block::fromArray(
            [
                'placeholders' => [
                    'main' => $placeholder,
                ],
            ]
        );

        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($placeholder),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(
                    [
                        'block' => $block,
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ]
                )
            )
            ->willReturn('rendered placeholder');

        self::assertSame(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                [
                    'twig_template' => $twigTemplate,
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
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderWithoutTwigTemplate(): void
    {
        $placeholder = new Placeholder();
        $block = Block::fromArray(
            [
                'placeholders' => [
                    'main' => $placeholder,
                ],
            ]
        );

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($placeholder),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(
                    [
                        'block' => $block,
                        'twig_template' => null,
                        'param' => 'value',
                    ]
                )
            )
            ->willReturn('rendered placeholder');

        self::assertSame(
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
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderWithViewContext(): void
    {
        $placeholder = new Placeholder();
        $block = Block::fromArray(
            [
                'placeholders' => [
                    'main' => $placeholder,
                ],
            ]
        );

        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($placeholder),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(
                    [
                        'block' => $block,
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ]
                )
            )
            ->willReturn('rendered placeholder');

        self::assertSame(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                [
                    'twig_template' => $twigTemplate,
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
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderWithViewContextFromTwigContext(): void
    {
        $placeholder = new Placeholder();
        $block = Block::fromArray(
            [
                'placeholders' => [
                    'main' => $placeholder,
                ],
            ]
        );

        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($placeholder),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(
                    [
                        'block' => $block,
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ]
                )
            )
            ->willReturn('rendered placeholder');

        self::assertSame(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                [
                    'view_context' => ViewInterface::CONTEXT_API,
                    'twig_template' => $twigTemplate,
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
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderReturnsEmptyStringOnException(): void
    {
        $block = Block::fromArray(['placeholders' => ['main' => new Placeholder()]]);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->willThrowException(new Exception());

        $renderedBlock = $this->runtime->renderPlaceholder(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class)
                ),
            ],
            $block,
            'main'
        );

        self::assertSame('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);
        $block = Block::fromArray(['placeholders' => ['main' => new Placeholder()]]);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

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
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItem(): void
    {
        $cmsItem = new CmsItem();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($cmsItem),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->willReturn('rendered item');

        self::assertSame(
            'rendered item',
            $this->runtime->renderItem(
                [],
                $cmsItem,
                'view_type',
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemWithViewContext(): void
    {
        $cmsItem = new CmsItem();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($cmsItem),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->willReturn('rendered item');

        self::assertSame(
            'rendered item',
            $this->runtime->renderItem(
                [],
                $cmsItem,
                'view_type',
                ['param' => 'value'],
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemWithViewContextFromTwigContext(): void
    {
        $cmsItem = new CmsItem();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($cmsItem),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->willReturn('rendered item');

        self::assertSame(
            'rendered item',
            $this->runtime->renderItem(
                [
                    'view_context' => ViewInterface::CONTEXT_API,
                ],
                $cmsItem,
                'view_type',
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemReturnsEmptyStringOnException(): void
    {
        $cmsItem = CmsItem::fromArray(['valueType' => 'value_type']);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($cmsItem),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->willThrowException(new Exception());

        self::assertSame(
            '',
            $this->runtime->renderItem(
                [],
                $cmsItem,
                'view_type',
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderItem
     */
    public function testRenderItemThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);

        $cmsItem = CmsItem::fromArray(['valueType' => 'value_type']);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($cmsItem),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value'])
            )
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderItem(
            [],
            $cmsItem,
            'view_type',
            ['param' => 'value']
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValue(): void
    {
        $condition = new Condition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['param' => 'value'])
            )
            ->willReturn('rendered value');

        self::assertSame(
            'rendered value',
            $this->runtime->renderValue(
                [],
                $condition,
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueWithViewContext(): void
    {
        $condition = new Condition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(['param' => 'value'])
            )
            ->willReturn('rendered value');

        self::assertSame(
            'rendered value',
            $this->runtime->renderValue(
                [],
                $condition,
                ['param' => 'value'],
                ViewInterface::CONTEXT_API
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueWithContextFromTwigContext(): void
    {
        $condition = new Condition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_API),
                self::identicalTo(['param' => 'value'])
            )
            ->willReturn('rendered value');

        self::assertSame(
            'rendered value',
            $this->runtime->renderValue(
                [
                    'view_context' => ViewInterface::CONTEXT_API,
                ],
                $condition,
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueReturnsEmptyStringOnException(): void
    {
        $condition = new Condition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['param' => 'value'])
            )
            ->willThrowException(new Exception());

        self::assertSame(
            '',
            $this->runtime->renderValue(
                [],
                $condition,
                ['param' => 'value']
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);

        $condition = new Condition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['param' => 'value'])
            )
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderValue(
            [],
            $condition,
            ['param' => 'value']
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getTemplateVariables
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderStringTemplate
     */
    public function testRenderStringTemplate(): void
    {
        $objectWithoutCast = Block::fromArray([]);
        $objectWithCast = new class() {
            public function __toString(): string
            {
                return 'foo';
            }
        };

        $renderedTemplate = $this->runtime->renderStringTemplate(
            '{{ foo }} {{ object }} {{ string }} {{ string2 }} {{ int }} {{ block }} {{ tpl1 }} {{ tpl2 }}',
            [
                'string' => 'bar',
                'int' => 42,
                'object' => $objectWithCast,
                'block' => $objectWithoutCast,
                'tpl3' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class),
                    ['string2' => 'baz']
                ),
                'tpl1' => $this->createMock(Template::class),
                'tpl2' => new TemplateWrapper(
                    $this->createMock(Environment::class),
                    $this->createMock(Template::class)
                ),
            ]
        );

        self::assertSame(' foo bar baz 42   ', $renderedTemplate);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getTemplateVariables
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderStringTemplate
     */
    public function testRenderStringTemplateWithAnError(): void
    {
        $renderedTemplate = $this->runtime->renderStringTemplate('abc{{ var ~ }}def', []);

        self::assertSame('', $renderedTemplate);
    }
}
