<?php

namespace Netgen\BlockManager\Tests\View\Renderer;

use Netgen\BlockManager\View\Renderer;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Twig_Environment;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $twigEnvironmentMock;

    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    protected $viewRenderer;

    public function setUp()
    {
        $this->viewBuilderMock = $this
            ->getMockBuilder(ViewBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->twigEnvironmentMock = $this
            ->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewRenderer = new Renderer($this->viewBuilderMock, $this->twigEnvironmentMock);
    }

    /**
     * @covers \Netgen\BlockManager\View\Renderer::__construct
     * @covers \Netgen\BlockManager\View\Renderer::renderView
     */
    public function testRenderView()
    {
        $view = new View();
        $view->setTemplate('some_template.html.twig');
        $view->setParameters(array('some_param' => 'some_value'));

        $this->twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('some_template.html.twig'),
                $this->equalTo(array('some_param' => 'some_value'))
            )
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->viewRenderer->renderView($view);

        self::assertEquals('rendered template', $renderedTemplate);
    }
}
