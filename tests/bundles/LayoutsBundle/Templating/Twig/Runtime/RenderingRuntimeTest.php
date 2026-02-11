<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Exception;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Block\PlaceholderList;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
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
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Stringable;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Template;
use Twig\TemplateWrapper;

#[CoversClass(RenderingRuntime::class)]
final class RenderingRuntimeTest extends TestCase
{
    private Stub&BlockService $blockServiceStub;

    private Stub&RendererInterface $rendererStub;

    private ErrorHandler $errorHandler;

    private RenderingRuntime $runtime;

    protected function setUp(): void
    {
        $this->blockServiceStub = self::createStub(BlockService::class);
        $this->rendererStub = self::createStub(RendererInterface::class);
        $localeProviderStub = self::createStub(LocaleProviderInterface::class);
        $this->errorHandler = new ErrorHandler();

        $this->runtime = new RenderingRuntime(
            $this->blockServiceStub,
            $this->rendererStub,
            $localeProviderStub,
            new RequestStack(),
            $this->errorHandler,
            new Environment(new ArrayLoader()),
        );
    }

    public function testRenderZone(): void
    {
        $zone = Zone::fromArray([]);
        $blocks = BlockList::fromArray([]);
        $layout = Layout::fromArray(['zones' => ZoneList::fromArray(['zone' => $zone])]);

        $twigTemplate = new ContextualizedTwigTemplate(self::createStub(Template::class));

        $this->blockServiceStub
            ->method('loadZoneBlocks')
            ->willReturn($blocks);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderBlock(): void
    {
        $block = new Block();
        $twigTemplate = new ContextualizedTwigTemplate(self::createStub(Template::class));

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderBlockWithoutTwigTemplate(): void
    {
        $block = new Block();

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderBlockWithViewContext(): void
    {
        $block = new Block();
        $twigTemplate = new ContextualizedTwigTemplate(self::createStub(Template::class));

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderBlockWithViewContextFromTwigContext(): void
    {
        $block = new Block();
        $twigTemplate = new ContextualizedTwigTemplate(self::createStub(Template::class));

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderBlockReturnsEmptyStringOnException(): void
    {
        $block = Block::fromArray(['id' => Uuid::v7(), 'definition' => new BlockDefinition()]);

        $this->rendererStub
            ->method('renderValue')
            ->willThrowException(new Exception());

        $renderedBlock = $this->runtime->renderBlock(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    self::createStub(Template::class),
                ),
            ],
            $block,
        );

        self::assertSame('', $renderedBlock);
    }

    public function testRenderBlockThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);
        $block = Block::fromArray(['id' => Uuid::v7(), 'definition' => new BlockDefinition()]);

        $this->rendererStub
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderBlock(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    self::createStub(Template::class),
                ),
            ],
            $block,
        );
    }

    public function testRenderPlaceholder(): void
    {
        $placeholder = new Placeholder();
        $block = Block::fromArray(
            [
                'id' => Uuid::v7(),
                'placeholders' => new PlaceholderList(['main' => $placeholder]),
            ],
        );

        $twigTemplate = new ContextualizedTwigTemplate(self::createStub(Template::class));

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderPlaceholderWithoutTwigTemplate(): void
    {
        $placeholder = new Placeholder();
        $block = Block::fromArray(
            [
                'id' => Uuid::v7(),
                'placeholders' => new PlaceholderList(['main' => $placeholder]),
            ],
        );

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderPlaceholderWithViewContext(): void
    {
        $placeholder = new Placeholder();
        $block = Block::fromArray(
            [
                'id' => Uuid::v7(),
                'placeholders' => new PlaceholderList(['main' => $placeholder]),
            ],
        );

        $twigTemplate = new ContextualizedTwigTemplate(self::createStub(Template::class));

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderPlaceholderWithViewContextFromTwigContext(): void
    {
        $placeholder = new Placeholder();
        $block = Block::fromArray(
            [
                'id' => Uuid::v7(),
                'placeholders' => new PlaceholderList(['main' => $placeholder]),
            ],
        );

        $twigTemplate = new ContextualizedTwigTemplate(self::createStub(Template::class));

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderPlaceholderReturnsEmptyStringOnException(): void
    {
        $block = Block::fromArray(['id' => Uuid::v7(), 'placeholders' => new PlaceholderList(['main' => new Placeholder()])]);

        $this->rendererStub
            ->method('renderValue')
            ->willThrowException(new Exception());

        $renderedBlock = $this->runtime->renderPlaceholder(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    self::createStub(Template::class),
                ),
            ],
            $block,
            'main',
        );

        self::assertSame('', $renderedBlock);
    }

    public function testRenderPlaceholderThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);
        $block = Block::fromArray(['id' => Uuid::v7(), 'placeholders' => new PlaceholderList(['main' => new Placeholder()])]);

        $this->rendererStub
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderPlaceholder(
            [
                'twig_template' => new ContextualizedTwigTemplate(
                    self::createStub(Template::class),
                ),
            ],
            $block,
            'main',
        );
    }

    public function testRenderItem(): void
    {
        $cmsItem = new CmsItem();

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderItemWithViewContext(): void
    {
        $cmsItem = new CmsItem();

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderItemWithViewContextFromTwigContext(): void
    {
        $cmsItem = new CmsItem();

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderItemReturnsEmptyStringOnException(): void
    {
        $cmsItem = CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type']);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderItemThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);

        $cmsItem = CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type']);

        $this->rendererStub
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderItem(
            [],
            $cmsItem,
            'view_type',
            ['param' => 'value'],
        );
    }

    public function testRenderResultWithViewTypeInItem(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => 'standard', 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item, 'subItem' => null, 'slot' => Slot::fromArray(['viewType' => 'overlay'])]);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderResultWithViewTypeInSlot(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item, 'subItem' => null, 'slot' => Slot::fromArray(['viewType' => 'overlay'])]);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderResultWithOverrideViewType(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => 'standard', 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item, 'subItem' => null, 'slot' => Slot::fromArray(['viewType' => 'overlay'])]);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderResultWithFallbackViewType(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item]);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderResultWithoutViewType(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item]);

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

    public function testRenderResultWithoutViewTypeInDebug(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('To render a result item, view type needs to be set through slot, or provided via "overrideViewType" or "fallbackViewType" parameters.');

        $this->errorHandler->setThrow(true);

        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item]);

        $this->runtime->renderResult(
            [],
            $result,
            null,
            null,
            ['param' => 'value'],
        );
    }

    public function testRenderResultWithViewContext(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item]);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderResultWithViewContextFromTwigContext(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item]);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderResultReturnsEmptyStringOnException(): void
    {
        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item]);

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderResultThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);

        $item = new ManualItem(Item::fromArray(['viewType' => null, 'cmsItem' => CmsItem::fromArray(['value' => 42, 'valueType' => 'value_type'])]));
        $result = Result::fromArray(['position' => 0, 'item' => $item]);

        $this->rendererStub
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderResult(
            [],
            $result,
            null,
            'view_type',
            ['param' => 'value'],
        );
    }

    public function testRenderValue(): void
    {
        $condition = new RuleCondition();

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderValueWithViewContext(): void
    {
        $condition = new RuleCondition();

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderValueWithContextFromTwigContext(): void
    {
        $condition = new RuleCondition();

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderValueReturnsEmptyStringOnException(): void
    {
        $condition = new RuleCondition();

        $this->rendererStub
            ->method('renderValue')
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

    public function testRenderValueThrowsExceptionInDebug(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $this->errorHandler->setThrow(true);

        $condition = new RuleCondition();

        $this->rendererStub
            ->method('renderValue')
            ->willThrowException(new Exception('Test exception text'));

        $this->runtime->renderValue(
            [],
            $condition,
            ['param' => 'value'],
        );
    }

    public function testRenderStringTemplate(): void
    {
        $objectWithoutCast = Block::fromArray(['id' => Uuid::v7()]);
        $objectWithCast = new class implements Stringable {
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
                    self::createStub(Template::class),
                    ['string2' => 'baz'],
                ),
                'tpl1' => self::createStub(Template::class),
                'tpl2' => new TemplateWrapper(
                    self::createStub(Environment::class),
                    self::createStub(Template::class),
                ),
            ],
        );

        self::assertSame(' foo bar baz 42   ', $renderedTemplate);
    }

    public function testRenderStringTemplateWithAnError(): void
    {
        $renderedTemplate = $this->runtime->renderStringTemplate('abc{{ var ~ }}def');

        self::assertSame('', $renderedTemplate);
    }
}
