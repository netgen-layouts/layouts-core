<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\View\Fragment\ViewRendererInterface as FragmentViewRendererInterface;
use Netgen\BlockManager\View\FragmentRenderer;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewRendererInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

final class FragmentRendererTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $viewBuilderMock;

    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $viewRendererMock;

    /**
     * @var \Symfony\Component\HttpKernel\Fragment\FragmentHandler&\PHPUnit\Framework\MockObject\MockObject
     */
    private $fragmentHandlerMock;

    /**
     * @var \Netgen\BlockManager\View\Fragment\ViewRendererInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $blockFragmentRendererMock;

    /**
     * @var \Netgen\BlockManager\View\FragmentRenderer
     */
    private $renderer;

    public function setUp(): void
    {
        $this->viewBuilderMock = $this
            ->createMock(ViewBuilderInterface::class);

        $this->viewRendererMock = $this
            ->createMock(ViewRendererInterface::class);

        $this->fragmentHandlerMock = $this
            ->createMock(FragmentHandler::class);

        $this->blockFragmentRendererMock = $this
            ->createMock(FragmentViewRendererInterface::class);

        $this->renderer = new FragmentRenderer(
            $this->viewBuilderMock,
            $this->viewRendererMock,
            $this->fragmentHandlerMock,
            [
                $this->blockFragmentRendererMock,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::__construct
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValue(): void
    {
        $view = new BlockView(new Block());

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with(new Block())
            ->will($this->returnValue($view));

        $this->blockFragmentRendererMock
            ->expects($this->once())
            ->method('supportsView')
            ->will($this->returnValue(true));

        $this->blockFragmentRendererMock
            ->expects($this->once())
            ->method('getController')
            ->will($this->returnValue(new ControllerReference('controller')));

        $this->fragmentHandlerMock
            ->expects($this->once())
            ->method('render')
            ->with($this->equalTo(new ControllerReference('controller')))
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->renderer->renderValue(new Block());

        $this->assertSame('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithNoControllerReference(): void
    {
        $view = new BlockView(new Block());

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with(new Block())
            ->will($this->returnValue($view));

        $this->blockFragmentRendererMock
            ->expects($this->once())
            ->method('supportsView')
            ->will($this->returnValue(true));

        $this->blockFragmentRendererMock
            ->expects($this->once())
            ->method('getController')
            ->will($this->returnValue(null));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($view))
            ->will($this->returnValue('rendered template'));

        $this->fragmentHandlerMock
            ->expects($this->never())
            ->method('render');

        $renderedTemplate = $this->renderer->renderValue(new Block());

        $this->assertSame('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithNonCacheableView(): void
    {
        $view = new LayoutView(new Layout());

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with(new Layout())
            ->will($this->returnValue($view));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($view))
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->renderer->renderValue(new Layout());

        $this->assertSame('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithCacheableViewAndDisabledCache(): void
    {
        $view = new BlockView(new Block());
        $view->setIsCacheable(false);

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with(new Block())
            ->will($this->returnValue($view));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($view))
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->renderer->renderValue(new Block());

        $this->assertSame('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithNoSupportedFragmentRenderer(): void
    {
        $view = new BlockView(new Block());

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with(new Block())
            ->will($this->returnValue($view));

        $this->blockFragmentRendererMock
            ->expects($this->once())
            ->method('supportsView')
            ->will($this->returnValue(false));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($view))
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->renderer->renderValue(new Block());

        $this->assertSame('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithNoFragmentRenderers(): void
    {
        $view = new BlockView(new Block());

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with(new Block())
            ->will($this->returnValue($view));

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderView')
            ->with($this->equalTo($view))
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->renderer->renderValue(new Block());

        $this->assertSame('rendered template', $renderedTemplate);
    }
}
