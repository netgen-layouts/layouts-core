<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\BlockView;

use Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetTwigBlockContentListener;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\TwigBlockDefinition;
use Netgen\Layouts\Event\RenderViewEvent;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Block\Stubs\TwigBlockDefinitionHandler;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use Twig\Template;

#[CoversClass(GetTwigBlockContentListener::class)]
final class GetTwigBlockContentListenerTest extends TestCase
{
    private GetTwigBlockContentListener $listener;

    protected function setUp(): void
    {
        $this->listener = new GetTwigBlockContentListener();
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [RenderViewEvent::getEventName('block') => 'onRenderView'],
            $this->listener::getSubscribedEvents(),
        );
    }

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

        $view = new BlockView($block);

        $twigTemplateStub = self::createStub(Template::class);

        $twigTemplateStub
            ->method('hasBlock')
            ->with(self::identicalTo('twig_block'))
            ->willReturn(true);

        $twigTemplateStub
            ->method('displayBlock')
            ->with(self::identicalTo('twig_block'))
            ->willReturnCallback(
                static function (): void {
                    echo 'rendered twig block';
                },
            );

        $view->addParameter('twig_template', new ContextualizedTwigTemplate($twigTemplateStub));

        $event = new RenderViewEvent($view);
        $this->listener->onRenderView($event);

        self::assertTrue($event->view->hasParameter('twig_content'));
        self::assertSame('rendered twig block', $event->view->getParameter('twig_content'));
    }

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

        $view = new BlockView($block);

        $twigTemplateStub = self::createStub(Template::class);

        $twigTemplateStub
            ->method('hasBlock')
            ->willReturnMap(
                [
                    ['block1', [], [], false],
                    ['block2', [], [], true],
                    ['block2', [], [], true],
                ],
            );

        $twigTemplateStub
            ->method('displayBlock')
            ->with(self::identicalTo('block2'))
            ->willReturnCallback(
                static function (): void {
                    echo 'rendered twig block';
                },
            );

        $view->addParameter('twig_template', new ContextualizedTwigTemplate($twigTemplateStub));

        $event = new RenderViewEvent($view);
        $this->listener->onRenderView($event);

        self::assertTrue($event->view->hasParameter('twig_content'));
        self::assertSame('rendered twig block', $event->view->getParameter('twig_content'));
    }

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

        $view = new BlockView($block);

        $twigTemplateStub = self::createStub(Template::class);

        $twigTemplateStub
            ->method('hasBlock')
            ->willReturn(false);

        $view->addParameter('twig_template', new ContextualizedTwigTemplate($twigTemplateStub));

        $event = new RenderViewEvent($view);
        $this->listener->onRenderView($event);

        self::assertTrue($event->view->hasParameter('twig_content'));
        self::assertSame('', $event->view->getParameter('twig_content'));
    }

    public function testOnRenderViewWithNoTwigBlock(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => new BlockDefinition(),
            ],
        );

        $view = new BlockView($block);
        $event = new RenderViewEvent($view);
        $this->listener->onRenderView($event);

        self::assertFalse($event->view->hasParameter('twig_content'));
    }

    public function testOnRenderViewInvalidTwigTemplate(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => new TwigBlockDefinition(),
            ],
        );

        $view = new BlockView($block);
        $view->addParameter('twig_template', new stdClass());

        $event = new RenderViewEvent($view);
        $this->listener->onRenderView($event);

        self::assertTrue($event->view->hasParameter('twig_content'));
        self::assertSame('', $event->view->getParameter('twig_content'));
    }

    public function testOnRenderViewWithNoTwigTemplate(): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'definition' => new TwigBlockDefinition(),
            ],
        );

        $view = new BlockView($block);

        $event = new RenderViewEvent($view);
        $this->listener->onRenderView($event);

        self::assertTrue($event->view->hasParameter('twig_content'));
        self::assertSame('', $event->view->getParameter('twig_content'));
    }

    public function testOnRenderViewWithNoBlockView(): void
    {
        $view = new View(new Value());
        $event = new RenderViewEvent($view);
        $this->listener->onRenderView($event);

        self::assertFalse($event->view->hasParameter('twig_content'));
    }
}
