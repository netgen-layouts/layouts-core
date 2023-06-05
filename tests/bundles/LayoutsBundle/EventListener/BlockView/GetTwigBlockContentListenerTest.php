<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\BlockView;

use Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\TwigBlockDefinition;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Block\Stubs\TwigBlockDefinitionHandler;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use Twig\Template;

use function sprintf;

final class GetTwigBlockContentListenerTest extends TestCase
{
    private GetTwigBlockContentListener $listener;

    protected function setUp(): void
    {
        $this->listener = new GetTwigBlockContentListener();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', LayoutsEvents::RENDER_VIEW, 'block') => 'onRenderView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderView(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => TwigBlockDefinition::fromArray(
                    [
                        'handler' => new TwigBlockDefinitionHandler(),
                    ],
                ),
            ],
        );

        $blockView = new BlockView($block);

        $twigTemplateMock = $this->createMock(Template::class);

        $twigTemplateMock
            ->method('hasBlock')
            ->with(self::identicalTo('twig_block'))
            ->willReturn(true);

        $twigTemplateMock
            ->expects(self::once())
            ->method('displayBlock')
            ->with(self::identicalTo('twig_block'))
            ->willReturnCallback(
                static function (): void {
                    echo 'rendered twig block';
                },
            );

        $blockView->addParameter('twig_template', new ContextualizedTwigTemplate($twigTemplateMock));

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertArrayHasKey('twig_content', $event->getParameters());
        self::assertSame('rendered twig block', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithBlockOnSecondPlace(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => TwigBlockDefinition::fromArray(
                    [
                        'handler' => new TwigBlockDefinitionHandler(['block1', 'block2']),
                    ],
                ),
            ],
        );

        $blockView = new BlockView($block);

        $twigTemplateMock = $this->createMock(Template::class);

        $twigTemplateMock
            ->method('hasBlock')
            ->willReturnMap(
                [
                    ['block1', [], [], false],
                    ['block2', [], [], true],
                    ['block2', [], [], true],
                ],
            );

        $twigTemplateMock
            ->expects(self::once())
            ->method('displayBlock')
            ->with(self::identicalTo('block2'))
            ->willReturnCallback(
                static function (): void {
                    echo 'rendered twig block';
                },
            );

        $blockView->addParameter('twig_template', new ContextualizedTwigTemplate($twigTemplateMock));

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertArrayHasKey('twig_content', $event->getParameters());
        self::assertSame('rendered twig block', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoBlocks(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => TwigBlockDefinition::fromArray(
                    [
                        'handler' => new TwigBlockDefinitionHandler(['block1', 'block2']),
                    ],
                ),
            ],
        );

        $blockView = new BlockView($block);

        $twigTemplateMock = $this->createMock(Template::class);

        $twigTemplateMock
            ->method('hasBlock')
            ->willReturn(false);

        $twigTemplateMock
            ->expects(self::never())
            ->method('displayBlock');

        $blockView->addParameter('twig_template', new ContextualizedTwigTemplate($twigTemplateMock));

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertArrayHasKey('twig_content', $event->getParameters());
        self::assertSame('', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoTwigBlock(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => new BlockDefinition(),
            ],
        );

        $blockView = new BlockView($block);
        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewInvalidTwigTemplate(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => new TwigBlockDefinition(),
            ],
        );

        $blockView = new BlockView($block);
        $blockView->addParameter('twig_template', new stdClass());

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertArrayHasKey('twig_content', $event->getParameters());
        self::assertSame('', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoTwigTemplate(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => new TwigBlockDefinition(),
            ],
        );

        $blockView = new BlockView($block);

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertArrayHasKey('twig_content', $event->getParameters());
        self::assertSame('', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }
}
