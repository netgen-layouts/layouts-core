<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\TwigBlockDefinition;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinitionHandler;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener;
use PHPUnit\Framework\TestCase;
use stdClass;
use Twig\Template;

final class GetTwigBlockContentListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->listener = new GetTwigBlockContentListener();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', BlockManagerEvents::RENDER_VIEW, 'block') => 'onRenderView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderView(): void
    {
        $block = Block::fromArray(
            [
                'id' => 42,
                'definition' => TwigBlockDefinition::fromArray(
                    [
                        'handler' => new TwigBlockDefinitionHandler(),
                    ]
                ),
            ]
        );

        $blockView = new BlockView($block);

        $twigTemplateMock = $this->createMock(Template::class);

        $twigTemplateMock
            ->expects(self::once())
            ->method('hasBlock')
            ->willReturn(true);

        $twigTemplateMock
            ->expects(self::once())
            ->method('displayBlock')
            ->willReturnCallback(function (): void { echo 'rendered twig block'; });

        $blockView->addParameter('twig_template', new ContextualizedTwigTemplate($twigTemplateMock));

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertArrayHasKey('twig_content', $event->getParameters());
        self::assertSame('rendered twig block', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoTwigBlock(): void
    {
        $block = Block::fromArray(
            [
                'id' => 42,
                'definition' => new BlockDefinition(),
            ]
        );

        $blockView = new BlockView($block);
        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewInvalidTwigTemplate(): void
    {
        $block = Block::fromArray(
            [
                'id' => 42,
                'definition' => new TwigBlockDefinition(),
            ]
        );

        $blockView = new BlockView($block);
        $blockView->addParameter('twig_template', new stdClass());

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertArrayHasKey('twig_content', $event->getParameters());
        self::assertSame('', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::getTwigBlockContent
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoTwigTemplate(): void
    {
        $block = Block::fromArray(
            [
                'id' => 42,
                'definition' => new TwigBlockDefinition(),
            ]
        );

        $blockView = new BlockView($block);

        $event = new CollectViewParametersEvent($blockView);
        $this->listener->onRenderView($event);

        self::assertArrayHasKey('twig_content', $event->getParameters());
        self::assertSame('', $event->getParameters()['twig_content']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetTwigBlockContentListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }
}
