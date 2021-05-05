<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Renderer;
use Netgen\Layouts\View\ViewBuilderInterface;
use Netgen\Layouts\View\ViewInterface;
use Netgen\Layouts\View\ViewRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RendererTest extends TestCase
{
    private MockObject $viewBuilderMock;

    private MockObject $viewRendererMock;

    private Renderer $renderer;

    protected function setUp(): void
    {
        $this->viewBuilderMock = $this
            ->createMock(ViewBuilderInterface::class);

        $this->viewRendererMock = $this
            ->createMock(ViewRendererInterface::class);

        $this->renderer = new Renderer(
            $this->viewBuilderMock,
            $this->viewRendererMock,
        );
    }

    /**
     * @covers \Netgen\Layouts\View\Renderer::__construct
     * @covers \Netgen\Layouts\View\Renderer::renderValue
     */
    public function testRenderValue(): void
    {
        $value = new Value();
        $view = new View($value);
        $view->setContext(ViewInterface::CONTEXT_APP);
        $view->setTemplate('some_template.html.twig');
        $view->addParameter('some_param', 'some_value');

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->with(
                self::identicalTo($value),
                self::identicalTo(ViewInterface::CONTEXT_APP),
                self::identicalTo(['some_param' => 'some_value']),
            )
            ->willReturn($view);

        $this->viewRendererMock
            ->expects(self::once())
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
