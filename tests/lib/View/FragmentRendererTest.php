<?php

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
use stdClass;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

final class FragmentRendererTest extends TestCase
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
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $fragmentHandlerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockFragmentRendererMock;

    /**
     * @var \Netgen\BlockManager\View\FragmentRenderer
     */
    private $renderer;

    public function setUp()
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
            array(
                $this->blockFragmentRendererMock,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage Fragment view renderer "stdClass" needs to implement "Netgen\BlockManager\View\Fragment\ViewRendererInterface" interface.
     */
    public function testConstructorThrowsInvalidInterfaceExceptionWithWrongInterface()
    {
        new FragmentRenderer(
            $this->viewBuilderMock,
            $this->viewRendererMock,
            $this->fragmentHandlerMock,
            array(new stdClass())
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::__construct
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValue()
    {
        $view = new BlockView();

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

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithNonCacheableView()
    {
        $view = new LayoutView();

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

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithCacheableViewAndDisabledCache()
    {
        $view = new BlockView();
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

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithNoSupportedFragmentRenderer()
    {
        $view = new BlockView();

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

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValue
     */
    public function testRenderValueWithNoFragmentRenderers()
    {
        $view = new BlockView();

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

        $this->assertEquals('rendered template', $renderedTemplate);
    }
}
