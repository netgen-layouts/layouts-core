<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\ViewRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

#[CoversClass(ViewRenderer::class)]
final class ViewRendererTest extends TestCase
{
    private Stub&Environment $twigEnvironmentStub;

    private ViewRenderer $viewRenderer;

    protected function setUp(): void
    {
        $eventDispatcherStub = self::createStub(EventDispatcherInterface::class);
        $this->twigEnvironmentStub = self::createStub(Environment::class);

        $this->viewRenderer = new ViewRenderer(
            $eventDispatcherStub,
            $this->twigEnvironmentStub,
        );
    }

    public function testRenderView(): void
    {
        $value = new Value();
        $view = new View($value);
        $view->template = 'some_template.html.twig';
        $view->addParameter('some_param', 'some_value');

        $this->twigEnvironmentStub
            ->method('render')
            ->willReturn('rendered template');

        $renderedTemplate = $this->viewRenderer->renderView($view);

        self::assertSame('rendered template', $renderedTemplate);
    }

    public function testRenderViewWithNoTemplate(): void
    {
        $view = new View(new Value());
        $view->addParameter('some_param', 'some_value');

        $renderedTemplate = $this->viewRenderer->renderView($view);

        self::assertSame('', $renderedTemplate);
    }
}
