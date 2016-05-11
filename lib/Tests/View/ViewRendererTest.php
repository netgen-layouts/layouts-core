<?php

namespace Netgen\BlockManager\Tests\View\Renderer;

use Netgen\BlockManager\View\ViewRenderer;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Twig_Environment;

class ViewRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $twigEnvironmentMock;

    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    protected $viewRenderer;

    public function setUp()
    {
        $this->twigEnvironmentMock = $this
            ->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewRenderer = new ViewRenderer($this->twigEnvironmentMock);
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewRenderer::__construct
     * @covers \Netgen\BlockManager\View\ViewRenderer::renderView
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
