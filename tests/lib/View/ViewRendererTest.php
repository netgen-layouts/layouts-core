<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\ViewRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class ViewRendererTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $eventDispatcherMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $twigEnvironmentMock;

    /**
     * @var \Netgen\BlockManager\View\ViewRenderer
     */
    private $viewRenderer;

    public function setUp(): void
    {
        $this->eventDispatcherMock = $this
            ->createMock(EventDispatcherInterface::class);

        $this->twigEnvironmentMock = $this
            ->createMock(Environment::class);

        $this->viewRenderer = new ViewRenderer(
            $this->eventDispatcherMock,
            $this->twigEnvironmentMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewRenderer::__construct
     * @covers \Netgen\BlockManager\View\ViewRenderer::renderView
     */
    public function testRenderView(): void
    {
        $view = new View(new Value());
        $view->setTemplate('some_template.html.twig');
        $view->addParameter('some_param', 'some_value');

        $this->eventDispatcherMock
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->equalTo(BlockManagerEvents::RENDER_VIEW),
                $this->isInstanceOf(CollectViewParametersEvent::class)
            );

        $this->eventDispatcherMock
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->equalTo(sprintf('%s.%s', BlockManagerEvents::RENDER_VIEW, 'stub')),
                $this->isInstanceOf(CollectViewParametersEvent::class)
            );

        $this->twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('some_template.html.twig'),
                $this->equalTo(
                    [
                        'some_param' => 'some_value',
                        'value' => new Value(),
                    ]
                )
            )
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->viewRenderer->renderView($view);

        $this->assertSame('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewRenderer::__construct
     * @covers \Netgen\BlockManager\View\ViewRenderer::renderView
     */
    public function testRenderViewWithNoTemplate(): void
    {
        $view = new View(new Value());
        $view->addParameter('some_param', 'some_value');

        $this->eventDispatcherMock
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->equalTo(BlockManagerEvents::RENDER_VIEW),
                $this->isInstanceOf(CollectViewParametersEvent::class)
            );

        $this->eventDispatcherMock
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->equalTo(sprintf('%s.%s', BlockManagerEvents::RENDER_VIEW, 'stub')),
                $this->isInstanceOf(CollectViewParametersEvent::class)
            );

        $this->twigEnvironmentMock
            ->expects($this->never())
            ->method('render');

        $renderedTemplate = $this->viewRenderer->renderView($view);

        $this->assertSame('', $renderedTemplate);
    }
}
