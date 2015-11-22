<?php

namespace Netgen\BlockManager\Tests\View\Renderer;

use Netgen\BlockManager\View\ViewRenderer;
use Netgen\BlockManager\Tests\View\Stubs\View;

class ViewRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\ViewRenderer::__construct
     * @covers \Netgen\BlockManager\View\ViewRenderer::renderView
     */
    public function testRenderView()
    {
        $view = new View();
        $view->setTemplate('some_template.html.twig');
        $view->setParameters(array('some_param' => 'some_value'));

        $twigEnvironmentMock = $this
            ->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('some_template.html.twig'),
                $this->equalTo(array('some_param' => 'some_value'))
            )
            ->will($this->returnValue('rendered template'));

        $viewRenderer = new ViewRenderer($twigEnvironmentMock);

        $renderedTemplate = $viewRenderer->renderView($view);

        self::assertEquals('rendered template', $renderedTemplate);
    }
}
