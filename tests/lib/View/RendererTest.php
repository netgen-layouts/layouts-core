<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Renderer;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\ViewRendererInterface;
use PHPUnit\Framework\TestCase;

final class RendererTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $viewBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $viewRendererMock;

    /**
     * @var \Netgen\BlockManager\View\Renderer
     */
    private $renderer;

    public function setUp(): void
    {
        $this->viewBuilderMock = $this
            ->createMock(ViewBuilderInterface::class);

        $this->viewRendererMock = $this
            ->createMock(ViewRendererInterface::class);

        $this->renderer = new Renderer(
            $this->viewBuilderMock,
            $this->viewRendererMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Renderer::__construct
     * @covers \Netgen\BlockManager\View\Renderer::renderValue
     */
    public function testRenderValue(): void
    {
        $view = new View(['value' => new Value()]);
        $view->setContext(ViewInterface::CONTEXT_API);
        $view->setTemplate('some_template.html.twig');
        $view->addParameter('some_param', 'some_value');

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with(new Value(), ViewInterface::CONTEXT_API, ['some_param' => 'some_value'])
            ->will($this->returnValue($view));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($view))
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->renderer->renderValue(
            new Value(),
            ViewInterface::CONTEXT_API,
            ['some_param' => 'some_value']
        );

        $this->assertEquals('rendered template', $renderedTemplate);
    }
}
