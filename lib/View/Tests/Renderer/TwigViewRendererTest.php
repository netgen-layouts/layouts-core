<?php

namespace Netgen\BlockManager\View\Tests\Renderer;

use Netgen\BlockManager\View\Renderer\TwigViewRenderer;
use Netgen\BlockManager\View\Tests\Stubs\View;
use PHPUnit_Framework_TestCase;

class TwigViewRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Renderer\TwigViewRenderer::__construct
     * @covers \Netgen\BlockManager\View\Renderer\TwigViewRenderer::renderView
     */
    public function testRenderView()
    {
        $view = new View();
        $view->setTemplate('some_template.html.twig');
        $view->setParameters(array('some_param' => 'some_value'));

        $twigEnvironmentMock = $this->getMock('Twig_Environment');
        $twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('some_template.html.twig'),
                $this->equalTo(array('some_param' => 'some_value'))
            )
            ->will($this->returnValue('rendered template'));

        $twigViewRenderer = new TwigViewRenderer($twigEnvironmentMock);

        $renderedTemplate = $twigViewRenderer->renderView($view);

        self::assertEquals('rendered template', $renderedTemplate);
    }
}
