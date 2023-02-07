<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\ViewRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class ViewRendererTest extends TestCase
{
    private MockObject $eventDispatcherMock;

    private MockObject $twigEnvironmentMock;

    private ViewRenderer $viewRenderer;

    protected function setUp(): void
    {
        $this->eventDispatcherMock = $this
            ->createMock(EventDispatcherInterface::class);

        $this->twigEnvironmentMock = $this
            ->createMock(Environment::class);

        $this->viewRenderer = new ViewRenderer(
            $this->eventDispatcherMock,
            $this->twigEnvironmentMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\View\ViewRenderer::__construct
     * @covers \Netgen\Layouts\View\ViewRenderer::renderView
     */
    public function testRenderView(): void
    {
        $value = new Value();
        $view = new View($value);
        $view->setTemplate('some_template.html.twig');
        $view->addParameter('some_param', 'some_value');

        $this->twigEnvironmentMock
            ->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo('some_template.html.twig'),
                self::identicalTo(
                    [
                        'value' => $value,
                        'some_param' => 'some_value',
                    ],
                ),
            )
            ->willReturn('rendered template');

        $renderedTemplate = $this->viewRenderer->renderView($view);

        self::assertSame('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\Layouts\View\ViewRenderer::__construct
     * @covers \Netgen\Layouts\View\ViewRenderer::renderView
     */
    public function testRenderViewWithNoTemplate(): void
    {
        $view = new View(new Value());
        $view->addParameter('some_param', 'some_value');

        $this->twigEnvironmentMock
            ->expects(self::never())
            ->method('render');

        $renderedTemplate = $this->viewRenderer->renderView($view);

        self::assertSame('', $renderedTemplate);
    }
}
