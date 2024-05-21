<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Locale\LocaleProviderInterface;
use Netgen\Layouts\Tests\Stubs\ErrorHandler;
use Netgen\Layouts\View\RendererInterface;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Template;
use Twig\TemplateWrapper;

final class RenderingRuntimeTest extends TestCase
{
    private MockObject $blockServiceMock;

    private MockObject $rendererMock;

    private MockObject $localeProviderMock;

    private ErrorHandler $errorHandler;

    private RenderingRuntime $runtime;

    protected function setUp(): void
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
            $this->errorHandler,
            new Environment(new ArrayLoader()),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderZone
     */
    public function testRenderZone(): void
    {
        $zone = Zone::fromArray([]);
        $blocks = new BlockList();
        $layout = Layout::fromArray(['zones' => new ArrayCollection(['zone' => $zone])]);

        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->blockServiceMock
            ->expects(self::once())
            ->method('loadZoneBlocks')
            ->with(
                self::identicalTo($zone),
                self::isNull(),
            )
            ->willReturn($blocks);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::isInstanceOf(ZoneReference::class),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(
                    [
                        'blocks' => $blocks,
                        'twig_template' => $twigTemplate,
                    ],
                ),
            )
            ->willReturn('rendered zone');

        self::assertSame(
            'rendered zone',
            $this->runtime->renderZone(
                $layout,
                'zone',
                ViewInterface::CONTEXT_DEFAULT,
                $twigTemplate,
            ),
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
                    ],
                ),
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
            ),
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
                    ],
                ),
            )
            ->willReturn('rendered block');

        self::assertSame(
            'rendered block',
            $this->runtime->renderBlock(
                [],
                $block,
                ['param' => 'value'],
            ),
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
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(
                    [
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ],
                ),
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
                ViewInterface::CONTEXT_APP,
            ),
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
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(
                    [
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ],
                ),
            )
            ->willReturn('rendered block');

        self::assertSame(
            'rendered block',
            $this->runtime->renderBlock(
                [
                    'view_context' => ViewInterface::CONTEXT_APP,
                    'twig_template' => $twigTemplate,
                ],
                $block,
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderBlock
     */
    public function testRenderBlockReturnsEmptyStringOnException(): void
    {
        $block = Block::fromArray(['id' => Uuid::uuid4(), 'definition' => new BlockDefinition()]);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->willThrowException(new Exception());

        $renderedBlock = $this->runtime->renderBlock(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class),
                ),
            ],
            $block,
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
        $block = Block::fromArray(['id' => Uuid::uuid4(), 'definition' => new BlockDefinition()]);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderBlock(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class),
                ),
            ],
            $block,
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
                'id' => Uuid::uuid4(),
                'placeholders' => [
                    'main' => $placeholder,
                ],
            ],
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
                    ],
                ),
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
            ),
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
                'id' => Uuid::uuid4(),
                'placeholders' => [
                    'main' => $placeholder,
                ],
            ],
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
                    ],
                ),
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
                ],
            ),
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
                'id' => Uuid::uuid4(),
                'placeholders' => [
                    'main' => $placeholder,
                ],
            ],
        );

        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($placeholder),
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(
                    [
                        'block' => $block,
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ],
                ),
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
                ViewInterface::CONTEXT_APP,
            ),
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
                'id' => Uuid::uuid4(),
                'placeholders' => [
                    'main' => $placeholder,
                ],
            ],
        );

        $twigTemplate = new ContextualizedTwigTemplate($this->createMock(Template::class));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($placeholder),
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(
                    [
                        'block' => $block,
                        'twig_template' => $twigTemplate,
                        'param' => 'value',
                    ],
                ),
            )
            ->willReturn('rendered placeholder');

        self::assertSame(
            'rendered placeholder',
            $this->runtime->renderPlaceholder(
                [
                    'view_context' => ViewInterface::CONTEXT_APP,
                    'twig_template' => $twigTemplate,
                ],
                $block,
                'main',
                [
                    'block' => $block,
                    'param' => 'value',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderPlaceholder
     */
    public function testRenderPlaceholderReturnsEmptyStringOnException(): void
    {
        $block = Block::fromArray(['id' => Uuid::uuid4(), 'placeholders' => ['main' => new Placeholder()]]);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->willThrowException(new Exception());

        $renderedBlock = $this->runtime->renderPlaceholder(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class),
                ),
            ],
            $block,
            'main',
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
        $block = Block::fromArray(['id' => Uuid::uuid4(), 'placeholders' => ['main' => new Placeholder()]]);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderPlaceholder(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    $this->createMock(Template::class),
                ),
            ],
            $block,
            'main',
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
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willReturn('rendered item');

        self::assertSame(
            'rendered item',
            $this->runtime->renderItem(
                [],
                $cmsItem,
                'view_type',
                ['param' => 'value'],
            ),
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
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willReturn('rendered item');

        self::assertSame(
            'rendered item',
            $this->runtime->renderItem(
                [],
                $cmsItem,
                'view_type',
                ['param' => 'value'],
                ViewInterface::CONTEXT_APP,
            ),
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
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willReturn('rendered item');

        self::assertSame(
            'rendered item',
            $this->runtime->renderItem(
                [
                    'view_context' => ViewInterface::CONTEXT_APP,
                ],
                $cmsItem,
                'view_type',
                ['param' => 'value'],
            ),
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
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willThrowException(new Exception());

        self::assertSame(
            '',
            $this->runtime->renderItem(
                [],
                $cmsItem,
                'view_type',
                ['param' => 'value'],
            ),
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
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderItem(
            [],
            $cmsItem,
            'view_type',
            ['param' => 'value'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultWithViewTypeInItem(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => 'standard', 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item, null, Slot::fromArray(['viewType' => 'overlay']));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($item),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'standard', 'param' => 'value']),
            )
            ->willReturn('rendered result');

        self::assertSame(
            'rendered result',
            $this->runtime->renderResult(
                [],
                $result,
                null,
                'view_type',
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultWithViewTypeInSlot(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item, null, Slot::fromArray(['viewType' => 'overlay']));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($item),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'overlay', 'param' => 'value']),
            )
            ->willReturn('rendered result');

        self::assertSame(
            'rendered result',
            $this->runtime->renderResult(
                [],
                $result,
                null,
                'view_type',
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultWithOverrideViewType(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => 'standard', 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item, null, Slot::fromArray(['viewType' => 'overlay']));

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($item),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willReturn('rendered result');

        self::assertSame(
            'rendered result',
            $this->runtime->renderResult(
                [],
                $result,
                'view_type',
                'fallback_view_type',
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultWithFallbackViewType(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($item),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willReturn('rendered result');

        self::assertSame(
            'rendered result',
            $this->runtime->renderResult(
                [],
                $result,
                null,
                'view_type',
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultWithoutViewType(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item);

        $this->rendererMock
            ->expects(self::never())
            ->method('renderValue');

        self::assertSame(
            '',
            $this->runtime->renderResult(
                [],
                $result,
                null,
                null,
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultWithoutViewTypeInDebug(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('To render a result item, view type needs to be set through slot, or provided via "overrideViewType" or "fallbackViewType" parameters.');

        $this->errorHandler->setThrow(true);

        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item);

        $this->rendererMock
            ->expects(self::never())
            ->method('renderValue');

        $this->runtime->renderResult(
            [],
            $result,
            null,
            null,
            ['param' => 'value'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultWithViewContext(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($item),
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willReturn('rendered result');

        self::assertSame(
            'rendered result',
            $this->runtime->renderResult(
                [],
                $result,
                null,
                'view_type',
                ['param' => 'value'],
                ViewInterface::CONTEXT_APP,
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultWithViewContextFromTwigContext(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($item),
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willReturn('rendered result');

        self::assertSame(
            'rendered result',
            $this->runtime->renderResult(
                [
                    'view_context' => ViewInterface::CONTEXT_APP,
                ],
                $result,
                null,
                'view_type',
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultReturnsEmptyStringOnException(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($item),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willThrowException(new Exception());

        self::assertSame(
            '',
            $this->runtime->renderResult(
                [],
                $result,
                null,
                'view_type',
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderResult
     */
    public function testRenderResultThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);

        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['valueType' => 'value_type'])]));
        $result = new Result(0, $item);

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($item),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['view_type' => 'view_type', 'param' => 'value']),
            )
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderResult(
            [],
            $result,
            null,
            'view_type',
            ['param' => 'value'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValue(): void
    {
        $condition = new RuleCondition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['param' => 'value']),
            )
            ->willReturn('rendered value');

        self::assertSame(
            'rendered value',
            $this->runtime->renderValue(
                [],
                $condition,
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueWithViewContext(): void
    {
        $condition = new RuleCondition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(['param' => 'value']),
            )
            ->willReturn('rendered value');

        self::assertSame(
            'rendered value',
            $this->runtime->renderValue(
                [],
                $condition,
                ['param' => 'value'],
                ViewInterface::CONTEXT_APP,
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueWithContextFromTwigContext(): void
    {
        $condition = new RuleCondition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(['param' => 'value']),
            )
            ->willReturn('rendered value');

        self::assertSame(
            'rendered value',
            $this->runtime->renderValue(
                [
                    'view_context' => ViewInterface::CONTEXT_APP,
                ],
                $condition,
                ['param' => 'value'],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getViewContext
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderValue
     */
    public function testRenderValueReturnsEmptyStringOnException(): void
    {
        $condition = new RuleCondition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['param' => 'value']),
            )
            ->willThrowException(new Exception());

        self::assertSame(
            '',
            $this->runtime->renderValue(
                [],
                $condition,
                ['param' => 'value'],
            ),
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

        $condition = new RuleCondition();

        $this->rendererMock
            ->expects(self::once())
            ->method('renderValue')
            ->with(
                self::identicalTo($condition),
                self::identicalTo(ViewInterface::CONTEXT_DEFAULT),
                self::identicalTo(['param' => 'value']),
            )
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderValue(
            [],
            $condition,
            ['param' => 'value'],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getTemplateVariables
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderStringTemplate
     */
    public function testRenderStringTemplate(): void
    {
        $objectWithoutCast = Block::fromArray(['id' => Uuid::uuid4()]);
        $objectWithCast = new class {
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
                    ['string2' => 'baz'],
                ),
                'tpl1' => $this->createMock(Template::class),
                'tpl2' => new TemplateWrapper(
                    $this->createMock(Environment::class),
                    $this->createMock(Template::class),
                ),
            ],
        );

        self::assertSame(' foo bar baz 42   ', $renderedTemplate);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::getTemplateVariables
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime::renderStringTemplate
     */
    public function testRenderStringTemplateWithAnError(): void
    {
        $renderedTemplate = $this->runtime->renderStringTemplate('abc{{ var ~ }}def');

        self::assertSame('', $renderedTemplate);
    }
}
