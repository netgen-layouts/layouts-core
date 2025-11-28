<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\ViewRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

#[CoversClass(ViewRenderer::class)]
final class ViewRendererTest extends TestCase
{
    private MockObject&Environment $twigEnvironmentMock;

    private ViewRenderer $viewRenderer;

    protected function setUp(): void
    {
        $eventDispatcherMock = $this
            ->createMock(EventDispatcherInterface::class);

        $this->twigEnvironmentMock = $this
            ->createMock(Environment::class);

        $this->viewRenderer = new ViewRenderer(
            $eventDispatcherMock,
            $this->twigEnvironmentMock,
        );
    }

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
                        'some_param' => 'some_value',
                        'value' => $value,
                    ],
                ),
            )
            ->willReturn('rendered template');

        $renderedTemplate = $this->viewRenderer->renderView($view);

        self::assertSame('rendered template', $renderedTemplate);
    }

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
