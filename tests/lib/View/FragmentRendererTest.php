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
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class FragmentRendererTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fragmentHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockFragmentRendererMock;

    /**
     * @var \Netgen\BlockManager\View\FragmentRenderer
     */
    protected $renderer;

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
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValueObject
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     */
    public function testRenderValueObject()
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

        $renderedTemplate = $this->renderer->renderValueObject(new Block());

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValueObject
     */
    public function testRenderValueObjectWithNonCacheableView()
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

        $renderedTemplate = $this->renderer->renderValueObject(new Layout());

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValueObject
     */
    public function testRenderValueObjectWithCacheableViewAndDisabledCache()
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

        $renderedTemplate = $this->renderer->renderValueObject(new Block());

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValueObject
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     */
    public function testRenderValueObjectWithNoSupportedFragmentRenderer()
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

        $renderedTemplate = $this->renderer->renderValueObject(new Block());

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\FragmentRenderer::renderValueObject
     * @covers \Netgen\BlockManager\View\FragmentRenderer::getFragmentViewRenderer
     */
    public function testRenderValueObjectWithNoFragmentRenderers()
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

        $renderedTemplate = $this->renderer->renderValueObject(new Block());

        $this->assertEquals('rendered template', $renderedTemplate);
    }
}
