<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Renderer;
use Netgen\Layouts\View\ViewBuilderInterface;
use Netgen\Layouts\View\ViewInterface;
use Netgen\Layouts\View\ViewRendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(Renderer::class)]
final class RendererTest extends TestCase
{
    private Stub&ViewBuilderInterface $viewBuilderStub;

    private Stub&ViewRendererInterface $viewRendererStub;

    private Renderer $renderer;

    protected function setUp(): void
    {
        $this->viewBuilderStub = self::createStub(ViewBuilderInterface::class);
        $this->viewRendererStub = self::createStub(ViewRendererInterface::class);

        $this->renderer = new Renderer(
            $this->viewBuilderStub,
            $this->viewRendererStub,
        );
    }

    public function testRenderValue(): void
    {
        $value = new Value();
        $view = new View($value);
        $view->context = ViewInterface::CONTEXT_APP;
        $view->template = 'some_template.html.twig';
        $view->addParameter('some_param', 'some_value');

        $this->viewBuilderStub
            ->method('buildView')
            ->with(
                self::identicalTo($value),
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(['some_param' => 'some_value']),
            )
            ->willReturn($view);

        $this->viewRendererStub
            ->method('renderView')
            ->with(self::identicalTo($view))
            ->willReturn('rendered template');

        $renderedTemplate = $this->renderer->renderValue(
            $value,
            ViewInterface::CONTEXT_APP,
            ['some_param' => 'some_value'],
        );

        self::assertSame('rendered template', $renderedTemplate);
    }
}
